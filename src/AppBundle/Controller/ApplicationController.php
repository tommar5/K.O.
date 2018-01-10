<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\ApplicationAgreement;
use AppBundle\Entity\ApplicationRepository;
use AppBundle\Entity\Comment;
use AppBundle\Entity\DateRestriction;
use AppBundle\Entity\FileUpload;
use AppBundle\Entity\TimeRestriction;
use AppBundle\Entity\User;
use AppBundle\Event\ApplicationStatusChangeEvent;
use AppBundle\Event\DocumentStatusChangeEvent;
use AppBundle\Form\ApplicationType;
use AppBundle\Form\Type\Application\ApplicationRejectType;
use DataDog\PagerBundle\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;

class ApplicationController extends Controller
{
    use DoctrineController;

    /**
     * @Route("/application")
     * @Method("GET")
     * @Template
     * @Security("is_granted('application-view', user)")
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        /** @var ApplicationRepository $repo */
        $repo = $this->get('em')->getRepository(Application::class);

        /** @var \Doctrine\ORM\QueryBuilder $applications */
        $applications = $repo->getApplicationsByRole($this->getUser());

        $filterService = $this->get('app.filter.type.service');

        return [
            'applications' => new Pagination(
                $applications, $request,
                [
                    'sorters' => ['t.createdAt' => 'desc'],
                    'applyFilter' => [$this, 'applicationsFilter'],
                ]
            ),
            'sportTypes' => $filterService->getSportsType(),
            'statuses' => $filterService->getApplicationStatuses(),
        ];
    }

    /**
     * @Route("/application/new")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("is_granted('application-new', user)")
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request)
    {
        $application = new Application();

        $lpExpressList = [];

        $form = $this->createForm($this->get('app.form.type.application'), $application, ['allow_extra_fields' => $lpExpressList]);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ((!$user->getMemberName() || !$user->getAddress() || !$user->getPhone()) && !$this->isGranted('application-new', $user)) {
            $this->addFlash("warning", $this->get('translator')->trans('application.flash.info_missing'));
        }

        if ($form->isValid()) {
            dump($form);
            die();
            if ($form->get('application_copy')->getData()) {
                foreach ($form->get('application_copy')->getData() as $item) {
                    if ($item) {
                        $document = new FileUpload('application_copy');
                        $document->setUser($user);
                        $document->setFile($item);
                        $application->addDocument($document);
                        $this->persist($document);
                    }
                }
            }

            $application->setCompetitionChiefConfirmed(false);
            $application->setSvoDelegateConfirmed(false);

            if ($this->isGranted('application-new', $user) && !$application->getId()) {
                $application->setUser($form->getData()->getLasfName());
                $application->setLasfName($form->getData()->getLasfName()->getMemberName());
            } else {
                $application->setUser($this->getUser());
            }

            $this->persist($application);
            $this->flush();
            $this->addFlash("success", $this->get('translator')->trans('application.flash.created'));

            $this->get('event_dispatcher')->dispatch(
                'application.status.changed',
                new ApplicationStatusChangeEvent($application)
            );

            return $this->redirectToRoute('app_application_index');
        }

        /** @var DateRestriction[] $datesArray */
        $datesArray = $this->getDoctrine()->getRepository('AppBundle:DateRestriction')->findAll();

        foreach ($datesArray as $date) {
            $datesArray[] = $date->getDate()->format('Y-m-d');
        }

        return [
            'application' => $application,
            'user' => $user,
            'form' => $form->createView(),
            'legalDates' => $datesArray,
        ];
    }

    /**
     * @Route("/application/{id}/modify")
     * @Method({"GET", "POST"})
     * @Security("is_granted('application-modify', user)")
     *
     * @Template
     *
     * @param Application $application
     * @param Request     $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function modifyAction(Application $application, Request $request)
    {
        /**
         * check if editor can view this application
         *
         * @var User $editor
         * */
        $editor = $this->getUser();
        if ($editor->hasRole(User::ROLE_LASF_COMMITTEE) && !in_array($application->getSport(), $editor->getSports()->toArray())) {
            return $this->redirectToRoute('app_application_index');
        }

        $user = $application->getUser();

        $lpExpressList = [];

        $form = $this->createForm($this->get('app.form.type.application'), $application, ['allow_extra_fields' => $lpExpressList]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($form->get('application_copy')->getData()) {
                foreach ($form->get('application_copy')->getData() as $item) {
                    if ($item) {
                        $document = new FileUpload('application_copy');
                        $document->setUser($user);
                        $document->setFile($item);
                        $application->addDocument($document);
                        $this->persist($document);
                    }
                }
            }

            $this->get('app.application_document_handler')->handleApplicationDocuments($application, $form);

            if ($application->isCompetitionChiefConfirmed() == null) {
                $application->setCompetitionChiefConfirmed(false);
            }

            if ($application->isSvoDelegateConfirmed() == null) {
                $application->setSvoDelegateConfirmed(false);
            }

            $this->persist($application);
            $this->flush();
            $this->addFlash("success", $this->get('translator')->trans('application.flash.created'));

            return $this->redirectToRoute('app_application_modify', ['id' => $application->getId()]);
        }

        $data = $this->get('em')->getRepository(Application::class)->createQueryBuilder('t')
            ->select('t, td, sub')
            ->leftJoin('t.documents', 'td')
            ->leftJoin('t.subCompetitions', 'sub')
            ->where('t.id = :id')
            ->setParameter('id', $application->getId());
        if (!$this->isGranted('application-modify-others', $this->getUser())) {
            $data->andWhere('t.user = :user')
                ->setParameter('user', $this->getUser());
        }
        $data = $data->add('orderBy', 't.id desc, sub.dateFrom ASC')
            ->getQuery()->getOneOrNullResult();

        if (is_null($data)) {
            if ($application->getUser()->getId() != $this->getUser()->getId()) {

                $this->addFlash("warning", $this->get('translator')->trans('application.flash.not_owner'));

                return $this->redirectToRoute('app_application_index');
            }
        }

        // Return available dates to view
        $datesArray = $this->getDoctrine()->getRepository('AppBundle:DateRestriction')->findAll();
        $timesArray = $this->getDoctrine()->getRepository(TimeRestriction::class)->find(1);

        /** @var DateRestriction[] $datesArray */
        foreach ($datesArray as $date) {
            $datesArray[] = $date->getDate()->format('Y-m-d');
        }

        if ($application->getDateTo() && ($application->getDateTo()->format('Y-m-d') < date('Y-m-d'))) {
            $this->addFlash('warning', $this->get('translator')->trans('application.flash.past_application'));
        }

        $comments = $this->get('em')->getRepository(Comment::class)->createQueryBuilder('c')
                         ->addSelect('c')
                         ->where('c.applicationId = :id')
                         ->andWhere('c.competitionId is NULL')
                         ->setParameter('id', $application->getId())
                         ->getQuery()->getResult();

        return [
            'application' => $data,
            'user' => $user,
            'currentUser' => $this->getUser(),
            'form' => $form->createView(),
            'legalTimes' => $timesArray,
            'legalDates' => $datesArray,
            'competitionChiefId' => $application->getCompetitionChief() ? $application->getCompetitionChief()->getUser()
                                                                                      ->getId() : null,
            'documents'          => $application->getAllTypeDocuments(),
            'comments'           => $comments,
        ];
    }


    /**
     * @Route("/application/{id}/delete")
     * @Method({"GET", "POST"})
     * @Security("is_granted('application-delete', user)")
     *
     * @param Application $application
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Application $application)
    {
        if ($application->getApplicationAgreement()) {
            $this->remove($application->getApplicationAgreement());
        }
        $this->remove($application);
        $this->flush();
        $this->addFlash('success', $this->get('translator')->trans('application.flash.deleted'));

        return $this->redirect($this->generateUrl('app_application_index'));
    }

    /**
     * @Route("/application/{id}/create-agreement")
     * @Method({"GET", "POST"})
     * @Security("is_granted('application-confirm', user)")
     * @param Application $application
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAgreementAction(Application $application)
    {
        $agreement = new ApplicationAgreement();
        $agreement->setApplication($application);
        $cmsBlock = $this->getDoctrine()->getRepository('AppBundle:CmsBlock')->findOneBy(
            ['alias' => 'default_agreement']
        );

        $timeRestrictions = $this->getDoctrine()->getRepository('AppBundle:TimeRestriction')->findAll()[0];
        $evaluatedBlock = $this->get('twig')->render(
            'AppBundle:ApplicationAgreement:agreement_eval.html.twig',
            [
                'template' => $cmsBlock->getContent(),
                'application' => $application,
                'timeRestrictions' => $timeRestrictions,
            ]
        );

        $agreement->setContent($evaluatedBlock);
        $application->setStatus(Application::STATUS_CONFIRMED);

        foreach ($application->getDocuments() as $document) {
            $document->setStatus(FileUpload::STATUS_APPROVED);
        }

        $this->persist($agreement);
        $this->flush();
        $this->get('event_dispatcher')->dispatch(
            'application.status.changed',
            new ApplicationStatusChangeEvent($application)
        );

        $this->addFlash('success', $this->get('translator')->trans('application.flash.confirmed'));

        return $this->redirectToRoute('app_applicationagreement_modify', ['id' => $agreement->getId()]);
    }


    /**
     * @Route("/application/{id}/decline")
     * @Template
     * @Security("is_granted('application-decline', user)")
     * @Method({"GET", "POST"})
     *
     * @param Application $application
     * @param Request $request
     *
     * @return array|JsonResponse
     */
    public function declineAction(Application $application, Request $request)
    {
        $form = $this->createForm(new ApplicationRejectType(), $application);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $application->setStatus(Application::STATUS_DECLINED);
            $this->flush();
            $this->get('event_dispatcher')->dispatch(
                'application.status.changed',
                new ApplicationStatusChangeEvent($application)
            );

            $this->addFlash('success', $this->get('translator')->trans('application.flash.declined'));

            return new JsonResponse([], 201);
        }

        return [
            'form' => $form->createView(),
            'application' => $application,
        ];
    }

    /**
     * @Route("/application/{id}/remind")
     * @Method("GET")
     * @Security("is_granted('invoice-confirm', user)")
     * @param Application $application
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remindAction(Application $application)
    {

        $this->get('mail')->user(
            $application->getUser(),
            'remind_not_paid_application',
            [
                '$application' => $application->getName(),
            ]
        );

        $this->addFlash('success', $this->get('translator')->trans('application.flash.remind_not_paid'));

        return $this->redirectToRoute('app_application_modify', ['id' => $application->getId()]);
    }

    /**
     * @Route("/application/{id}/mark_as_paid")
     * @Method({"GET", "POST"})
     * @Security("is_granted('invoice-confirm', user)")
     * @param Application $application
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function markAsPaid(Application $application)
    {
        $application->setStatus(Application::STATUS_PAID);
        $this->flush();
        $this->get('event_dispatcher')->dispatch(
            'application.status.changed',
            new ApplicationStatusChangeEvent($application)
        );

        $this->addFlash('success', $this->get('translator')->trans('application.flash.paid'));

        return $this->redirectToRoute('app_application_index');
    }

    /**
     * @Route("/application/{id}/remove_invoice")
     * @Method({"GET", "POST"})
     * @Security("is_granted('invoice-delete', user)")
     * @param Application $application
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeInvoice(Application $application)
    {
        $application->setStatus(Application::STATUS_NOT_PAID);

        foreach ($application->getDocuments() as $doc) {
            if ($doc->getType() == FileUpload::TYPE_INVOICE) {
                $application->removeDocument($doc);
            }
        }

        $this->flush();
        $this->addFlash('success', $this->get('translator')->trans('application.flash.invoice_removed'));

        return $this->redirectToRoute('app_application_modify', ['id' => $application->getId()]);
    }

    /**
     * @Route("/application/{id}/remove-document/{document}")
     * @Method({"GET", "POST"})
     * @param Application $application
     * @param FileUpload $document
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeDocument(Application $application, FileUpload $document)
    {
        $isOwner = $document->getUser() == $this->getUser();
        if ($application->getDocuments()->contains($document) && ($this->isGranted('document-remove', $this->getUser()) || $isOwner)) {
            $application->removeDocument($document);
            $this->remove($document);
            $this->flush();
            $this->addFlash('success', $this->get('translator')->trans('application.flash.document_removed'));
        } else {
            $this->addFlash('danger', $this->get('translator')->trans('application.flash.document_not_removed'));
        }

        return $this->redirectToRoute('app_application_modify', ['id' => $application->getId()]);
    }

    /**
     * @Route("/{applicationId}/{id}/approve")
     * @ParamConverter("application", class="AppBundle:Application", options={"id" = "applicationId"})
     * @Method({"GET", "POST"})
     * @Security("is_granted('approve-document', user)")
     * @param Application $application
     * @param FileUpload $fileUpload
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function approveDocument(Application $application, FileUpload $fileUpload, Request $request)
    {

        if ($request->isXmlHttpRequest()) {

            $this->get('app.application_document_handler')->approveApplicationDocument($application, $fileUpload);

            return new JsonResponse(200);
        }

        return new JsonResponse(400);
    }

    /**
     * @Route("/{id}/remind-upload-document/{type}")
     * @Security("is_granted('remind-upload-document', user)")
     * @param Application $application
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function remindUploadDocument(Application $application, $type)
    {
        $this->get('app.application_document_handler')->remindUploadDocument($application, 'application', $type, $this->get('mail'));
        $this->addFlash(
            'success',
            $this->get('translator')->trans('application.flash.remind_upload_document')
        );

        return $this->redirectToRoute('app_application_modify', ['id' => $application->getId()]);
    }

    /**
     * @Route("/application/chief/assigned-to-me")
     * @Method({"GET", "POST"})
     * @Template("AppBundle:Application:show_assigned_to_me.html.twig")
     * @Security("is_granted('application-assigned-to-me', user)")
     * @param Request $request
     *
     * @return array
     */
    public function showAssignedToMeAction(Request $request)
    {
        $user = $this->getUser();
        $data = $this->get('em')->getRepository(Application::class)->createQueryBuilder('t')
            ->addSelect('cc', 's')
            ->leftJoin('t.competitionChief', 'cc')
            ->leftJoin('t.sport', 's')
            ->where('cc.user = :user')
            ->orderBy('t.id', 'desc')
            ->setParameter('user', $user);

        return [
            'applications' => new Pagination($data, $request),
        ];
    }

    /**
     * @Route("/application/{id}/toggle-chief-confirmed")
     * @Method({"GET", "POST"})
     * @Security("is_granted('competition-chief-confirm', user)")
     * @param Application $application
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function toggleChiefConfirmedAction(Application $application)
    {
        $application->setCompetitionChiefConfirmed(!$application->isCompetitionChiefConfirmed());
        $this->flush();
        $this->addFlash(
            'success',
            $this->get('translator')->trans('application.competition_chief_assigned.flash.toggled')
        );

        return $this->redirectToRoute('app_application_showassignedtome');
    }

    /**
     * @Route("/application/{id}/toggle-svo-confirmed")
     * @Method({"GET", "POST"})
     * @Security("is_granted('svo-delegate-confirm', user)")
     * @param Application $application
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function toggleSvoConfirmedAction(Application $application)
    {
        $application->setSvoDelegateConfirmed(!$application->isSvoDelegateConfirmed());
        $this->flush();
        $this->addFlash('success', $this->get('translator')->trans('application.flash.svo_toggled'));

        return $this->redirectToRoute('app_application_modify', ['id' => $application->getId()]);
    }

    /**
     * @Route("/events")
     * @Method({"GET"})
     */
    public function getCalendarEvents()
    {
        /** @var ApplicationRepository $repo */
        $repo = $this->get('em')->getRepository(Application::class);

        $applications = $repo->getApplicationsByRole($this->getUser())->getQuery()->getResult();

        $eventData = [];
        /** @var Application $application */
        foreach ($applications as $application) {
            if ($application->getDateFrom() && $application->getDateTo()) {
                $eventData[] = [
                    'title' => $application->getName(),
                    'start' => $application->getDateFrom()->format(\DateTime::ISO8601),
                    'end' => date('Y-m-d H:i:s', strtotime($application->getDateTo()->format('Y-m-d') . ' + 1 day')),
                    'url' => $this->generateUrl('app_application_modify', ['id' => $application->getId()]),
                ];
            }
        }

        return new JsonResponse($eventData);
    }

    /**
     * @Route("/application/member/{id}/")
     * @Method({"GET"})
     * @Security("is_granted('competition-member-view', user)")
     * @param User $user
     *
     * @return JsonResponse
     */
    public function infoAction(User $user)
    {
        if (!$user->getId()
            || !$user->getMemberName()
            || !$user->getAddress()
            || !$user->getPhone()
        ) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse(
            [
                'member' => $user->getId(),
                'memberName' => $user->getMemberName(),
                'address' => $user->getAddress(),
                'vatCode' => $user->getVatCode(),
                'memberCode' => $user->getMemberCode(),
                'bank' => $user->getBank(),
                'bankAccount' => $user->getBankAccount(),
                'phone' => $user->getPhone(),
                'email' => $user->getEmail()
            ]
        );
    }

    /**
     * @param QueryBuilder $qb
     * @param $key
     * @param $val
     */
    public function applicationsFilter(QueryBuilder $qb, $key, $val)
    {
        if (!$val) {
            return ;
        }

        switch ($key) {
            case 't.name':
                $qb->andWhere($qb->expr()->like('t.name', ':name'));
                $qb->setParameter('name', "%$val%");
                break;
            case 't.dateFrom':
                $qb->andWhere($qb->expr()->like('t.dateFrom', ':dt'));
                $qb->setParameter('dt', "%$val%");
                break;
            case 't.location':
                $qb->andWhere($qb->expr()->like('t.location', ':location'));
                $qb->setParameter('location', "%$val%");
                break;
            case 't.sport':
                $qb->andWhere('t.sport = :sport');
                $qb->setParameter('sport', $val);
                break;
            case 't.status':
                $qb->andWhere('t.status = :status');
                $qb->setParameter('status', $val);
                break;
            case 't.user':
                $qb->addSelect('u');
                $qb->leftJoin('t.user', 'u');
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like(
                            $qb->expr()->concat('u.firstname', $qb->expr()->concat($qb->expr()->literal(' '), 'u.lastname')), ':uname'),
                        $qb->expr()->like('u.memberName', ':uname')
                    ));
                $qb->setParameter('uname', "%$val%");
                break;
        }
    }

}
