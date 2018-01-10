<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CompetitionChief;
use AppBundle\Entity\MusicStyle;
use AppBundle\Form\CompetitionChiefType;
use DataDog\PagerBundle\Pagination;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class CompetitionChiefController extends Controller
{
    use DoctrineController;

    /**
     * @Route("/competition-chief")
     * @Method("GET")
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $competitionChiefs  = $this->get('em')->getRepository(CompetitionChief::class)->createQueryBuilder('t');

        return [
            'competitionChiefs' => new Pagination($competitionChiefs, $request, [
                'limit' => 20,
            ]),
        ];
    }

    /**
     * @Route("/competition-chief/new")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $competitionChief = new CompetitionChief();

        $form = $this->createForm(new CompetitionChiefType(), $competitionChief);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $isChief = null;

            $isChief = $this->getDoctrine()->getRepository(CompetitionChief::class)->findOneBy(['user' => $form->getData()->getUser()->getId()]);

            if ($isChief != null) {

                $this->addFlash("warning", $this->get('translator')->trans('competition_chief.flash.chief_already_exists'));

                return $this->redirectToRoute('app_competitionchief_new');

            } else {

                $this->persist($competitionChief);
                $this->flush();
                $this->addFlash("success", $this->get('translator')->trans('competition_chief.flash.created'));

                return $this->redirectToRoute('app_competitionchief_index');
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/competition-chief/{id}/edit")
     * @Method({"GET", "POST"})
     * @Template
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @param CompetitionChief $competitionChief
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(CompetitionChief $competitionChief, Request $request)
    {

        $form = $this->createForm(new CompetitionChiefType(), $competitionChief);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->persist($competitionChief);
            $this->flush();
            $this->addFlash("success", $this->get('translator')->trans('competition_chief.flash.edited'));

            return $this->redirectToRoute('app_competitionchief_index');
        }

        return [
            'chief' => $competitionChief,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/competition-chief/{id}/delete")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param CompetitionChief $competitionChief
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(CompetitionChief $competitionChief)
    {
        if(count($competitionChief->getApplications())){
            $this->addFlash('warning', $this->get('translator')->trans('competition_chief.flash.has_application_cant_delete'));
            return $this->redirectToRoute('app_competitionchief_index');
        }

        foreach($competitionChief->getSports() as $sport){
            $competitionChief->removeSport($sport);
        }

        $this->remove($competitionChief);
        $this->flush();
        $this->addFlash('success', $this->get('translator')->trans('competition_chief.flash.deleted'));

        return $this->redirect($this->generateUrl('app_competitionchief_index'));
    }
}
