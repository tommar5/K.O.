<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Steward;
use AppBundle\Form\StewardType;
use DataDog\PagerBundle\Pagination;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class StewardController extends Controller
{
    use DoctrineController;

    /**
     * @Route("/steward")
     * @Method("GET")
     * @Template
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_JUDGE_COMMITTEE')")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $stewards = $this->get('em')->getRepository(Steward::class)->createQueryBuilder('t');

        return [
            'stewards' => new Pagination($stewards, $request, [
                'limit' => 20,
            ]),
        ];
    }

    /**
     * @Route("/steward/new")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_JUDGE_COMMITTEE')")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $steward = new Steward();

        $form = $this->createForm(new StewardType(), $steward);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $isSteward = null;

            $isSteward = $this->getDoctrine()->getRepository(Steward::class)->findOneBy(['user' => $form->getData()->getUser()->getId()]);

            if ($isSteward != null) {

                $this->addFlash("warning", $this->get('translator')->trans('steward.flash.steward_already_exists'));

                return $this->redirectToRoute('app_steward_new');

            } else {

                $this->persist($steward);
                $this->flush();
                $this->addFlash("success", $this->get('translator')->trans('steward.flash.created'));

                return $this->redirectToRoute('app_steward_index');
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/steward/{id}/edit")
     * @Method({"GET", "POST"})
     * @Template
     *
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_JUDGE_COMMITTEE')")
     * @param Steward $steward
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Steward $steward, Request $request)
    {

        $form = $this->createForm(new StewardType(), $steward);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->persist($steward);
            $this->flush();
            $this->addFlash("success", $this->get('translator')->trans('steward.flash.edited'));

            return $this->redirectToRoute('app_steward_index');
        }

        return [
            'steward' => $steward,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/steward/{id}/delete")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param Steward $steward
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Steward $steward)
    {
        try {
            if ($steward->getApplications()->count() > 0) {
                $this->addFlash('danger', $this->get('translator')->trans('steward.flash.has_application_cant_delete'));

                return $this->redirectToRoute('app_steward_index');
            }

            $this->remove($steward);
            $this->flush();
            $this->addFlash('success', $this->get('translator')->trans('steward.flash.deleted'));
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash('warning', $this->get('translator')->trans('steward.flash.has_application_cant_delete'));
        }

        return $this->redirectToRoute('app_steward_index');
    }
}
