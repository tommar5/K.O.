<?php namespace AppBundle\Controller;

use AppBundle\Entity\MusicStyle;
use AppBundle\Form\MusicStyleType;
use DataDog\PagerBundle\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MusicStyleController extends Controller implements VerifyTermsInterface
{
    use DoctrineController;

    /**
     * @Route("/music-style")
     * @Method("GET")
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $musicStyles = $this->get('em')->getRepository('AppBundle:MusicStyle')->createQueryBuilder('s');

        return [
            'musicStyles' => new Pagination($musicStyles, $request),
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
        $musicStyle = new MusicStyle();
        $form = $this->createForm(new MusicStyleType(), $musicStyle);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return [
                'entity' => $musicStyle,
                'form' => $form->createView(),
            ];
        }

        $this->persist($musicStyle);
        $this->flush();
        $this->addFlash("success", $this->get('translator')->trans('sport.flash.created'));

        return $this->redirectToRoute('app_musicstyle_index');
    }

    /**
     * @Route("/{id}/edit")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     * @param MusicStyle $musicStyle
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(MusicStyle $musicStyle, Request $request)
    {
        $form = $this->createForm(new MusicStyleType(), $musicStyle);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return [
                'form' => $form->createView(),
                'entity' => $musicStyle,
            ];
        }

        $this->persist($musicStyle);
        $this->flush();
        $this->addFlash("success", $this->get('translator')->trans('sport.flash.updated'));

        return $this->redirectToRoute('app_musicstyle_index');
    }

    /**
     * @Route("/{id}/delete")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     * @param MusicStyle $musicStyle
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(MusicStyle $musicStyle)
    {
        $this->remove($musicStyle);
        $this->flush();
        $this->addFlash("success", $this->get('translator')->trans('sport.flash.removed'));

        return $this->redirectToRoute('app_musicstyle_index');
    }
}
