<?php

namespace AppBundle\Controller;

use AppBundle\Form\SafetyChiefType;
use AppBundle\Entity\Application;
use AppBundle\Entity\SafetyChief;
use Symfony\Component\HttpFoundation\Request;
use DataDog\PagerBundle\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SafetyChiefController extends Controller
{
    use DoctrineController;

    /**
     * @Route("/safety-chiefs")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_SECRETARY') or has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $safetyChiefs = new SafetyChief();
        $form = $this->createForm(new SafetyChiefType($this->getUser()), $safetyChiefs);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->persist($safetyChiefs);
            $this->flush();
            $this->addFlash("success", $this->get('translator')->trans('safety_chief.flash.created'));

            return $this->redirectToRoute('app_safetychief_index');
        }


        $safetyChiefs = $this->get('em')->getRepository(SafetyChief::class)->createQueryBuilder('s');

        return [
            'chiefs' => new Pagination($safetyChiefs, $request),
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/safety-chiefs/{id}/delete")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Template
     * @param SafetyChief $safetyChiefs
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(SafetyChief $safetyChiefs)
    {

        $isRelation = $this->get('em')->getRepository(Application::class)->findOneBy(['safetyChief' => $safetyChiefs->getId()]);

        if ($isRelation) {
            $this->addFlash('warning', $this->get('translator')->trans('safety_chief.flash.has_a_relation').($isRelation->getId()));

            return $this->redirect($this->generateUrl('app_safetychief_index'));
        }

        $this->remove($safetyChiefs);
        $this->flush();
        $this->addFlash('success', $this->get('translator')->trans('safety_chief.flash.deleted'));

        return $this->redirect($this->generateUrl('app_safetychief_index'));
    }

}
