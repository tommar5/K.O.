<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TimeRestriction;
use AppBundle\Form\TimeRestrictionType;
use DataDog\PagerBundle\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TimeRestrictionController extends Controller
{

    use DoctrineController;

    /**
     * @Route("/time-restriction/{id}")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("is_granted('upload-terms', user)")
     *
     * @param Request         $request
     * @param TimeRestriction $timeRestrictions
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(TimeRestriction $timeRestrictions, Request $request)
    {

        $form = $this->createForm(new TimeRestrictionType(), $timeRestrictions);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->persist($timeRestrictions);
            $this->flush();
            $this->addFlash('success', $this->get('translator')->trans('time_restriction.flash.modified'));

            return $this->redirectToRoute('app_timerestriction_index', ['id' => 1]);
        }

        $data = $this
            ->get('em')
            ->getRepository(TimeRestriction::class)
            ->createQueryBuilder('t')
            ->getQuery()
            ->getResult();

        return [
            'times' => $data,
            'form'  => $form->createView(),
        ];
    }
}
