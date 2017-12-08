<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DateRestriction;
use AppBundle\Form\DateRestrictionType;
use DataDog\PagerBundle\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DateRestrictionController extends Controller
{
    use DoctrineController;

    /**
     * @Route("/date-restriction")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_SECRETARY')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $dateRestrictions = new DateRestriction();
        $form             = $this->createForm(new DateRestrictionType($this->getUser()), $dateRestrictions);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $checkExistingDate = false;

            if ($form->getData()->getDate()) {
                $date              = new \DateTime($form->getData()->getDate()->format('Y-m-d'));
                $checkExistingDate = $this
                    ->getDoctrine()
                    ->getRepository(DateRestriction::class)
                    ->findOneBy(['date' => $date]);
            }

            if (!$checkExistingDate) {
                $this->persist($dateRestrictions);
                $this->flush();
                $this->addFlash("success", $this->get('translator')->trans('date_restriction.flash.created'));

                return $this->redirectToRoute('app_daterestriction_index');
            } else {
                $this->addFlash("warning", $this->get('translator')->trans('date_restriction.flash.already_exists'));

                return $this->redirectToRoute('app_daterestriction_index');
            }
        }

        $dateRestrictions = $this->get('em')->getRepository(DateRestriction::class)->createQueryBuilder('t')->orderBy('t.date', 'DESC');

        return [
            'dates' => new Pagination($dateRestrictions, $request),
            'form'  => $form->createView(),
        ];
    }

    /**
     * @Route("/date-restriction/{id}/delete")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_SECRETARY')")
     * @Template
     *
     * @param DateRestriction $dateRestrictions
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(DateRestriction $dateRestrictions)
    {
        $this->remove($dateRestrictions);
        $this->flush();
        $this->addFlash('success', $this->get('translator')->trans('date_restriction.flash.deleted'));

        return $this->redirect($this->generateUrl('app_daterestriction_index'));
    }
}
