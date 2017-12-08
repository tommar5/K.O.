<?php namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Form\CmsBlockType;
use AppBundle\Entity\CmsBlock;
use DataDog\PagerBundle\Pagination;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ApplicationAgreementModifyType;

/**
 * @Route("/cms")
 */
class CmsBlockController extends Controller implements VerifyTermsInterface
{
    use DoctrineController;

    /**
     * @Route("")
     * @Method("GET")
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $blocks = $this->get('em')->getRepository('AppBundle:CmsBlock')->createQueryBuilder('t');

        return [
            'blocks' => new Pagination($blocks, $request, [
                'applyFilter' => [$this, 'cmsFilters'],
            ]),
        ];
    }

    /**
     * Displays a form to create a new CmsBlock entity.
     *
     * @Route("/new")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $block = new CmsBlock();
        $form = $this->createForm(new CmsBlockType(), $block);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return [
                'entity' => $block,
                'form' => $form->createView(),
            ];
        }

        $this->persist($block);
        $this->flush();
        $this->addFlash("success", $this->get('translator')->trans('cms_block.flash.created'));

        return $this->redirectToRoute('app_cmsblock_index');
    }


    /**
     * Displays a form to edit an existing CmsBlock entity.
     *
     * @Route("/{id}/edit")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param CmsBlock $block
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(CmsBlock $block, Request $request)
    {
        $form = $this->createForm(new CmsBlockType(), $block);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return [
                'form' => $form->createView(),
                'entity' => $block,
            ];
        }
        $this->persist($block);
        $this->flush();
        $this->addFlash("success", $this->get('translator')->trans('cms_block.flash.updated'));

        return $this->redirectToRoute('app_cmsblock_index');
    }

    /**
     * Deletes a CmsBlock entity.
     *
     * @Route("/{id}/delete")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param CmsBlock $block
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(CmsBlock $block)
    {
        $this->remove($block);
        $this->flush();
        $this->addFlash("danger", $this->get('translator')->trans('cms_block.flash.removed'));

        return $this->redirectToRoute('app_cmsblock_index');
    }

    /**
     * @Route("/{id}/modify-agreement", name="app_cmsblock_modify")
     * @Template("AppBundle:CmsBlock:application_agreement_modify.html.twig")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param CmsBlock $block
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function modifyAgreementAction(CmsBlock $block, Request $request)
    {
        $form = $this->createForm(new ApplicationAgreementModifyType(), $block);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->flush();
            $this->addFlash("success", $this->get('translator')->trans('application_agreement.flash.modified'));
            return $this->redirectToRoute('app_cmsblock_index');
         }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @route("/{id}/restore-agreement", name="app_cmsblock_restore")
     * @security("has_role('ROLE_ADMIN')")
     * @param CmsBlock $block
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function restoreDefaultAgreementAction(CmsBlock $block)
    {
        $location = $this->container->get('kernel')->locateResource("@AppBundle/Resources/views/blocks/layout/default_agreement.html.twig");
        $block->setContent(file_get_contents($location));
        $this->flush();

        $this->addFlash("success", $this->get('translator')->trans('application_agreement.flash.restored'));
        return $this->redirectToRoute('app_cmsblock_index');
    }

    /**
     * Our filter handler function, which allows us to
     * modify the query builder specifically for our filter option
     * @param QueryBuilder $qb
     * @param string $key
     * @param string $val
     */
    public function cmsFilters(QueryBuilder $qb, $key, $val)
    {
        if (empty($val)) {
            return;
        }

        switch ($key) {
        case 't.alias':
            $qb->andWhere($qb->expr()->like('t.alias', ':alias'));
            $qb->setParameter('alias', "%$val%");
            break;
        case 't.name':
            $qb->andWhere($qb->expr()->like('t.name', ':name'));
            $qb->setParameter('name', "%$val%");
            break;
        case 't.updatedAt':
            $date = date("Y-m-d", strtotime($val));
            $qb->andWhere($qb->expr()->gt('t.updatedAt', "'$date 00:00:00'"));
            $qb->andWhere($qb->expr()->lt('t.updatedAt', "'$date 23:59:59'"));
            break;
        }
    }
}
