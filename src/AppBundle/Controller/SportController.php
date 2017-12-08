<?php namespace AppBundle\Controller;

use AppBundle\Entity\Sport;
use AppBundle\Form\SportType;
use DataDog\PagerBundle\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SportController extends Controller implements VerifyTermsInterface
{
    use DoctrineController;

    /**
     * @Route("/sport")
     * @Method("GET")
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $sports = $this->get('em')->getRepository('AppBundle:Sport')->createQueryBuilder('s');

        return [
            'sports' => new Pagination($sports, $request),
        ];
    }

    /**
     * @Route("/new")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $sport = new Sport();
        $form = $this->createForm(new SportType(), $sport);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return [
                'entity' => $sport,
                'form' => $form->createView(),
            ];
        }

        $this->persist($sport);
        $this->flush();
        $this->addFlash("success", $this->get('translator')->trans('sport.flash.created'));

        return $this->redirectToRoute('app_sport_index');
    }

    /**
     * @Route("/{id}/edit")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     * @param Sport $sport
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Sport $sport, Request $request)
    {
        $form = $this->createForm(new SportType(), $sport);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return [
                'form' => $form->createView(),
                'entity' => $sport,
            ];
        }

        $this->persist($sport);
        $this->flush();
        $this->addFlash("success", $this->get('translator')->trans('sport.flash.updated'));

        return $this->redirectToRoute('app_sport_index');
    }

    /**
     * @Route("/{id}/delete")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Sport $sport
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Sport $sport)
    {
        $this->remove($sport);
        $this->flush();
        $this->addFlash("success", $this->get('translator')->trans('sport.flash.removed'));

        return $this->redirectToRoute('app_sport_index');
    }
}