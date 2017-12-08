<?php namespace AppBundle\Controller;

use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use AppBundle\Form\UserSearchType;
use DataDog\PagerBundle\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller implements VerifyTermsInterface, ChangePasswordInterface
{
    /**
     * @Route("/")
     * @Method("GET")
     * @param Request $request
     * @Template
     * @return array
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(new UserSearchType());
        $form->handleRequest($request);

        $licencesRepo = $this->get('em')->getRepository(Licence::class);
        $licences = $licencesRepo->getValidLicences(Licence::$driverTypes);

        if ($form->isValid()) {
            $licences = $licencesRepo->filterLicencesByName($licences, trim($form->getData()['q']));
        }

        $licences->orderBy('u.lastname');

        $lasfMembers = $licencesRepo->getValidLicences(Licence::TYPE_MEMBERSHIP)
            ->getQuery()
            ->getResult();

        return [
            'licences' => new Pagination($licences, $request),
            'lasfMembers' => $lasfMembers,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/judges")
     * @Template("AppBundle:Dashboard:index.html.twig")
     * @param Request $request
     * @return array
     */
    public function judgesAction(Request $request)
    {
        $form = $this->createForm(new UserSearchType(), ['judges' => true]);
        $form->handleRequest($request);

        $licencesRepo = $this->get('em')->getRepository(Licence::class);
        $licences = $licencesRepo->getValidLicences(Licence::$judgeTypes);

        if ($form->isValid()) {
            $licences = $licencesRepo->filterLicencesByName($licences, trim($form->getData()['q']));
        }

        $licences->orderBy('u.lastname');

        $lasfMembers = $licencesRepo->getValidLicences(Licence::TYPE_MEMBERSHIP)
            ->getQuery()
            ->getResult();

        return [
            'licences' => new Pagination($licences, $request),
            'lasfMembers' => $lasfMembers,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/help")
     * @Template
     */
    public function helpAction()
    {
        $users = $this->get('em')->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.enabled = 1')
            ->where('BIT_AND(u.roles, :role) > 0')
            ->setParameter('role', User::$roleMap['ROLE_ADMIN'])
            ->getQuery()->getResult();

        return [
            'users' => $users,
        ];
    }

    /**
     * @Route("/terms")
     * @Template
     */
    public function termsAction()
    {
        return [];
    }

    /**
     * @Route("/privacy-policy")
     * @Template
     */
    public function privacyAction()
    {
        return [];
    }
}
