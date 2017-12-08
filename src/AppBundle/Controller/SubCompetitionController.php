<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Application;
use AppBundle\Entity\SubCompetition;
use AppBundle\Entity\DateRestriction;
use AppBundle\Entity\FileUpload;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\TimeRestriction;
use AppBundle\Entity\Comment;

class SubCompetitionController extends Controller
{

    use DoctrineController;

    /**
     * @Route("/sub-competition/{id}/new", name="app_sub_competition_new")
     * @Security("is_granted('application-new', user)")
     * @param Request $request
     * @param Application $application
     * @template("AppBundle:SubCompetition:new.html.twig")
     * @return array
     */
    public function newSubCompetitionAction(Request $request, Application $application)
    {
        $subCompetition = new SubCompetition();
        $subCompetition->setApplication($application);
        /** @var DateRestriction[] $datesArray */
        $datesArray = $this->getDoctrine()->getRepository('AppBundle:DateRestriction')->findAll();
        foreach ($datesArray as $date) {
            $datesArray[] = $date->getDate()->format('Y-m-d');
        }

        $form = $this->createForm($this->get('app.form.type.sub_competition'), $subCompetition);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $application->addSubCompetition($subCompetition);

            $this->persist($subCompetition);
            $this->flush();

            return $this->redirectToRoute('app_application_modify', ['id' => $application->getId()]);
        }

        return [
            'application' => $application,
            'form' => $form->createView(),
            'legalDates' => $datesArray,
        ];
    }

    /**
     * @Route("/application/{id}/sub-competition/{competition_id}/modify", name="app_sub_competition_modify")
     * @ParamConverter("competition", options={"mapping": {"competition_id": "id"}})
     *
     * @param Request $request
     * @param Application $application
     * @param SubCompetition $competition
     *
     * @template("AppBundle:SubCompetition:modify.html.twig")
     * @return array
     */
    public function modifySubCompetitionAction(Request $request, Application $application, SubCompetition $competition)
    {
        /** @var DateRestriction[] $datesArray */
        $datesArray = $this->getDoctrine()->getRepository('AppBundle:DateRestriction')->findAll();
        $timesArray = $this->getDoctrine()->getRepository(TimeRestriction::class)->find(1);
        foreach ($datesArray as $date) {
            $datesArray[] = $date->getDate()->format('Y-m-d');
        }

        $form = $this->createForm($this->get('app.form.type.sub_competition'), $competition);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->get('app.application_document_handler')->handleSubCompetitionDocuments($competition, $form);

            $this->persist($competition);
            $this->flush();

            return $this->redirectToRoute('app_sub_competition_modify', [
                'id' => $application->getId(), 'competition_id' => $competition->getId()
            ]);
        }

        $comments = $this->get('em')->getRepository(Comment::class)->createQueryBuilder('c')
            ->addSelect('c')
            ->where('c.applicationId = :id')
            ->andWhere('c.competitionId = :competitionId')
            ->setParameters([
                'id'=> $application->getId(),
                'competitionId' => $competition->getId(),
            ])
            ->getQuery()->getResult();

        return [
            'application' => $application,
            'competition' => $competition,
            'form' => $form->createView(),
            'legalDates' => $datesArray,
            'legalTimes' => $timesArray,
            'documents' => $competition->getAllTypeDocuments(),
            'comments' => $comments,
        ];
    }

    /**
     * @route("/sub-competition/{id}/delete", name="app_sub_competition_delete")
     * @param SubCompetition $subCompetition
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteSubCompetitionAction(SubCompetition $subCompetition)
    {
        $this->remove($subCompetition);
        $this->flush();

        return $this->redirect($this->container->get('request')->headers->get('referer'));
    }


    /**
     * @Route("sub-competition/{id}/approve-document/{document}", name="sub_competition_approve_document")
     * @Method({"GET", "POST"})
     * @Security("is_granted('approve-document', user)")
     * @param SubCompetition $subCompetition
     * @param FileUpload $document
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function approveDocument(SubCompetition $subCompetition, FileUpload $document, Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $this->get('app.application_document_handler')->approveSubCompetitionDocument($subCompetition, $document);

            return new JsonResponse(200);
        }

        return new JsonResponse(400);
    }

    /**
     * @Route("/sub-competition/{id}/remind-upload-document/{type}", name="sub_competition_remind_upload_document")
     * @Method({"GET", "POST"})
     * @Security("is_granted('remind-upload-document', user)")
     * @param SubCompetition $subCompetition
     * @param string $type
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function remindUploadDocument(SubCompetition $subCompetition, $type)
    {
        $this->get('app.application_document_handler')->remindUploadDocument($subCompetition, 'sub-competition', $type, $this->get('mail'));

        $this->addFlash(
            'success',
            $this->get('translator')->trans('application.flash.remind_upload_document')
        );

        return $this->redirectToRoute('app_sub_competition_modify', [
            'id' => $subCompetition->getApplication()->getId(),
            'competition_id' => $subCompetition->getId()
        ]);
    }

    /**
     * @Route("/sub-competition/{id}/remove-document/{document}", name="app_sub_competition_remove_document")
     * @Method({"GET", "POST"})
     * @param SubCompetition $subCompetition
     * @param FileUpload $document
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeDocument(SubCompetition $subCompetition, FileUpload $document)
    {
        $isOwner = $document->getUser() == $this->getUser();
        if ($subCompetition->getDocuments()->contains($document) && ($this->isGranted('document-remove', $this->getUser()) || $isOwner)) {
            $subCompetition->removeDocument($document);
            $this->remove($document);
            $this->flush();
            $this->addFlash('success', $this->get('translator')->trans('application.flash.document_removed'));
        } else {
            $this->addFlash('danger', $this->get('translator')->trans('application.flash.document_not_removed'));
        }

        return $this->redirectToRoute('app_sub_competition_modify', [
            'id' => $subCompetition->getApplication()->getId(),
            'competition_id' => $subCompetition->getId()
        ]);
    }
}
