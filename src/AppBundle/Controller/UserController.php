<?php namespace AppBundle\Controller;

use AppBundle\Entity\Language;
use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use AppBundle\Event\UserCreateEvent;
use AppBundle\Form\Type\User\ProfileType;
use AppBundle\Form\Type\User\TeamMemberType;
use AppBundle\Form\UserType;
use DataDog\PagerBundle\Pagination;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\DateFormatter\IntlDateFormatter;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller implements VerifyTermsInterface, ChangePasswordInterface
{
    use DoctrineController;

    /**
     * @Route("/user")
     * @Method("GET")
     * @Template
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_DECLARANT') or has_role('ROLE_JUDGE_COMMITTEE')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $users = $this->get('em')->getRepository(User::class)->createQueryBuilder('u');

        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            $users->andWhere('u.parent = :parent')->setParameter('parent', $this->getUser());
        }

        $filterService = $this->get('app.filter.type.service');
        return [
            'users' => new Pagination($users, $request, [
                'applyFilter' => [$this, 'userFilters'],
                'sorters' => ['u.createdAt' => 'desc'],
            ]),
            'legalTypes' => $filterService->getLegalTypes(),
            'genderTypes' => $filterService->getGenderTypes(),
            'languageTypes' => $filterService->getLanguageTypes(),
        ];
    }

    /**
     * @Route("/profile")
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function profileAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(new ProfileType($this->getUser()), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('app.registrator')->updateUser($user);
            $this->flush();

            $this->addFlash('success', $this->get('translator')->trans('user.flash.profile_updated'));
            return $this->redirect($this->generateUrl('app_user_profile'));
        }

        return [
            'form' => $form->createView(),
            ];
    }

    /**
     * @Route("/user/{id}/resend")
     * @Method({"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param User $person
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function resendAction(User $person)
    {
        if ($person->isTermsConfirmed()) {
            throw $this->createNotFoundException();
        }

        $reg = $this->get('app.registrator');
        $pass = $reg->createRandomString();
        $person->setPlainPassword($pass);
        $reg->updateUser($person);

        $this->flush();

        $this->get('app.user_create.listener')->notifyUser(new UserCreateEvent($person));

        $this->addFlash('success', $this->get('translator')->trans('user.flash.info_resent'));
        $this->addFlash('info', $this->get('translator')->trans('user.flash.generated_password') . ' ' . $pass);

        return $this->redirect($this->generateUrl('app_user_edit', ['id' => $person->getId()]));
    }

    /**
     * @Route("/user/new")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_DECLARANT')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new UserType($this->getUser()), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $registrator = $this->get('app.registrator');

            if (!$this->getUser()->hasRole('ROLE_ADMIN') || strlen($user->getPlainPassword()) == 0) {
                $password = $registrator->createRandomString(8);

                if (strlen($user->getPlainPassword()) == 0) {
                    $this->addFlash('info', $this->get('translator')->trans('user.flash.generated_password') . ' ' . $password);
                }

                $user->setPlainPassword($password);
            }

            $this->assignUserRoles($user, $form);
            $registrator->registerUser($this->getUser(), $user);
            $this->persist($user);
            $this->flush();

            $this->addFlash('success', $this->get('translator')->trans('user.flash.created'));

            $this->get('event_dispatcher')->dispatch('user.created', new UserCreateEvent($user));

            return $this->redirectToRoute('app_user_index');
        }

        return [
            'entity' => $user,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/user/{id}/edit")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_ADMIN') or (has_role('ROLE_DECLARANT') and person.isParent(user))")
     *
     * @param User $person
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(User $person, Request $request)
    {
        $form = $this->createForm(new UserType($this->getUser()), $person);

        foreach ($person->getRoles() as $role) {
            if ($form->has($role)) {
                $form->get($role)->setData(true);
            }
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->assignUserRoles($person, $form);
            $registrator = $this->get('app.registrator');
            $registrator->editingUser($person);
            $this->persist($person);

            if ($this->getUser()->hasRole("ROLE_DECLARANT") || $this->getUser()->hasRole("ROLE_ADMIN")) {
                /** @var User $declarant */
                $declarant = $this->getUser();

                if ($form->getNormData()->getParent()) {
                    $declarant = $form->getNormData()->getParent();
                }

                if ($person->hasRole('ROLE_RACER')) {
                    $declarant->addMember($person);
                }
            }

            $this->flush();
            $this->addFlash('success', $this->get('translator')->trans('user.flash.profile_updated'));

            return $this->redirectToRoute('app_user_index');
        }

        return [
            'form' => $form->createView(),
            'entity' => $person,
        ];
    }

    /**
     * @Route("/user/{id}/confirm")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmAction(User $user)
    {

        $this->persist($user);

        $user->setConfirmed(true);

        $this->flush();

        $this->get('mail')->user($user, 'user_confirmed', [
            'link' => $this->generateUrl('app_auth_login', [], true)
        ]);

        $this->addFlash('success', $this->get('translator')->trans('user.flash.profile_updated'));

        return $this->redirect($this->generateUrl('app_user_edit', ['id' => $user->getId()]));

    }


    /**
     * @Route("/user/{id}/delete")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(User $user)
    {
        if(!$user->isEnabled()) {
            if ($user->getParent()) {
                $user->setParent(null);
                $this->flush();
                return $this->redirect($this->generateUrl('app_user_delete', ['id' => $user->getId()]));
            }

            if (count($user->getLicences()) > 0) {
                foreach ($user->getLicences() as $licence) {
                    $this->remove($licence);
                }
                $this->flush();
                return $this->redirect($this->generateUrl('app_user_delete', ['id' => $user->getId()]));
            }

            if (count($user->getDocuments()) > 0) {
                foreach ($user->getDocuments() as $document) {
                    $this->remove($document);
                }
                $this->flush();
                return $this->redirect($this->generateUrl('app_user_delete', ['id' => $user->getId()]));
            }

            $this->remove($user);
            $this->flush();
            $this->addFlash('success', $this->get('translator')->trans('user.flash.deleted'));
        } else{
            $this->addFlash('danger', $this->get('translator')->trans('user.flash.error_deleting'));
        }
        return $this->redirect($this->generateUrl('app_user_index'));
    }

    /**
     * @Route("/profile/{id}")
     * @Method({"GET"})
     * @Template
     *
     * @param User $person
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userAction(User $person)
    {
        if (!$person->isEnabled()) {
            throw $this->createNotFoundException();
        }

        $licences = $this->get('em')->getRepository(Licence::class)->createQueryBuilder('l')
            ->addSelect('lb', 'tr')
            ->join('l.user', 'u')
            ->leftJoin('l.basedOnLicence', 'lb')
            ->leftJoin('l.representatives', 'tr')
            ->where('u.enabled = 1')
            ->andWhere('l.expiresAt >= :now')
            ->andWhere('l.status IN(:status)')
            ->andWhere('u = :user')
            ->orderBy('l.expiresAt', 'desc')
            ->setParameter('now', (new \DateTime())->format('Y-m-d'))
            ->setParameter('status', Licence::$completedStatuses)
            ->setParameter('user', $person)
            ->getQuery()->getResult();

        return [
            'user' => $person,
            'licences' => $licences
        ];
    }

    /**
     * @Route("/my-racers")
     * @Method({"GET"})
     * @Template
     * @Security("has_role('ROLE_DECLARANT')")
     */
    public function racersAction()
    {
        $racers = $this->get('em')->getRepository(User::class)->createQueryBuilder('u')
            ->select('u, ul, ull')
            ->join('u.declarants', 'ud')
            ->leftJoin('u.licences', 'ul')
            ->leftJoin('ul.licence', 'ull')
            ->where('ud = :ud')
            ->andWhere('u.enabled = 1')
            ->setParameter('ud', $this->getUser())
            ->getQuery()->getResult();

        return [
            'racers' => $racers
        ];
    }

    /**
     * @Route("/my-racers/add")
     * @Method({"GET", "POST"})
     * @Template
     * @Security("has_role('ROLE_DECLARANT')")
     * @param Request $request
     * @return array|JsonResponse
     */
    public function addRacerAction(Request $request)
    {
        $form = $this->createForm(new TeamMemberType($this->getUser()));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $member = $form->get('member')->getData();

            if (!$this->getUser()->getMembers()->contains($member)) {
                $this->getUser()->getMembers()->add($member);
                $this->get('em')->flush();
            }

            $this->addFlash('success', $this->get('translator')->trans('user.my_racers.flash.added'));
            return new JsonResponse([], 201);
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/my-racers/{id}/remove")
     * @Method({"GET"})
     * @Security("has_role('ROLE_DECLARANT')")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeRacerAction(User $user)
    {
        if ($this->getUser()->getMembers()->contains($user)) {
            $this->getUser()->removeMember($user);
            $this->get('em')->flush();
        }

        $this->addFlash('success', $this->get('translator')->trans('user.my_racers.flash.removed'));

        return $this->redirectToRoute('app_user_racers');
    }

    /**
     * Our filter handler function, which allows us to
     * modify the query builder specifically for our filter option
     * @param QueryBuilder $qb
     * @param string $key
     * @param string $val
     */
    public function userFilters(QueryBuilder $qb, $key, $val)
    {
        if (empty($val)) {
            return;
        }

        switch ($key) {
            case 'declarant':
                $qb->join('u.parent', 'p');
                $qb->andWhere($qb->expr()->like('p.memberName', ':memberName'));
                $qb->setParameter('memberName', "%$val%");
                break;
            case 'team':
                $qb->join('u.licences', 'l')->join('l.licence', 'pl');
                $qb->andWhere($qb->expr()->like('pl.teamName', ':teamName'));
                $qb->andWhere('l.type IN(:driverTypes)');
                $qb->setParameter('teamName', "%$val%");
                $qb->setParameter('driverTypes', Licence::$driverTypes);
                break;
            case 'u.email':
                $qb->andWhere($qb->expr()->like('u.email', ':email'));
                $qb->setParameter('email', "%$val%");
                break;
            case 'u.city':
                $qb->andWhere($qb->expr()->like('u.city', ':city'));
                $qb->setParameter('city', "%$val%");
                break;
            case 'u.language':
                $qb->select('u, l')->leftJoin('u.languages','l');
                $qb->andWhere($qb->expr()->like('l.language', ':language'));
                $qb->setParameter('language', "%$val%");
                break;
            case 'u.secondaryLanguage':
                $qb->andWhere($qb->expr()->like('u.secondaryLanguage', ':secondaryLanguage'));
                $qb->setParameter('secondaryLanguage', "%$val%");
                break;
            case 'u.gender':
                $qb->andWhere('u.gender = :gender');
                $qb->setParameter('gender', $val);
                break;
            case 'name':
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like(
                            $qb->expr()->concat('u.firstname', $qb->expr()->concat($qb->expr()->literal(' '), 'u.lastname')), ':uname'),
                        $qb->expr()->like('u.memberName', ':uname')
                    )
                );
                $qb->setParameter('uname', "%$val%");
                break;
            case 'u.legal':
                switch ($val) {
                    case 'natural':
                        $qb->andWhere('u.legal = 0');
                        break;
                    case 'legal-all':
                        $qb->andWhere('u.legal = 1');
                        break;
                    case 'legal':
                        $qb->andWhere('u.legal = 1');
                        $qb->andWhere('u.associated = 0');
                        break;
                    case 'legal-associated':
                        $qb->andWhere('u.legal = 1');
                        $qb->andWhere('u.associated = 1');
                        break;
                }
                break;
        }
    }

    /**
     * Generate Users CSV
     * @Route("/generate-csv", name="generate-users-csv")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function generateCSVAction()
    {
        $response = new StreamedResponse();
        $response->setCallback([$this, 'getUsersCSVFile']);

        $response->setStatusCode(200);
        $response->headers->set('Content-Transfer-Encoding:', 'UTF-8');
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="users_csv.csv"');

        return $response;
    }

    /**
     * Gather User Data for CSV
     */
    public function getUsersCSVFile()
    {

        $trans = $this->get('translator');

        $handle = fopen('php://output', 'w+');

        // Making UTF-8 CSV for Excel (bom = byte order mark)
        fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        // Add the header of the CSV file
        fputcsv($handle,
            [
                $trans->trans('user.label.email'),
                $trans->trans('user.label.firstname'),
                $trans->trans('user.label.lastname'),
                $trans->trans('user.label.about_me'),
                $trans->trans('user.label.notes'),
                $trans->trans('user.label.registered_on'),
                $trans->trans('user.label.phone'),
                $trans->trans('user.label.address'),
                $trans->trans('user.label.associated'),
                $trans->trans('user.label.bank'),
                $trans->trans('user.label.bank_account'),
                $trans->trans('user.label.member_name'),
                $trans->trans('user.label.member_code'),
                $trans->trans('user.label.vat_code'),
            ], ';'
        );

        // Create lt-LT local date formater
        $dateFormatter = datefmt_create(
            $this->getParameter('locale'),
            IntlDateFormatter::SHORT,
            IntlDateFormatter::SHORT,
            $this->getParameter('locale_timezone'),
            IntlDateFormatter::GREGORIAN,
            'Y-MM-dd HH:mm' // 1970-01-01 07:00
        );

        // Get all users from database
        $repository = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        // Get data from each user
        foreach ($repository as $user) {
            fputcsv(
                $handle, // The file pointer
                [
                    $user->getEmail(),
                    $user->getFirstname(),
                    $user->getLastname(),
                    $user->getAboutMe(),
                    $user->getNotes(),
                    $dateFormatter->format($user->getCreatedAt()),
                    $user->getPhone(),
                    $user->getAddress(),
                    $this->associatedStatus($user->isAssociated()),
                    $user->getBank(),
                    $user->getBankAccount(),
                    $user->getMemberName(),
                    $user->getMemberCode(),
                    $user->getVatCode()
                ], ';'
            );
        }

        fclose($handle);
    }

    /**
     * Helper Function to get name of Associated status
     * @param $status
     * @return mixed
     */
    public function associatedStatus ($status)
    {
        if ($status) {
            return $this->get('translator')->trans('user.edit.accociated');
        }
        if ($status === false) {
            return $this->get('translator')->trans('user.edit.not_accociated');
        }
        return $this->get('translator')->trans('user.edit.association_undefined');
    }

    /**
     * @param $user
     * @param $form
     */
    private function assignUserRoles(User $user, Form $form)
    {
        $user->setRoles([]);

        foreach (User::$roleMap as $role => $roleValue) {
            if (!$form->has($role)) {
                continue;
            }

            if ($form->get($role)->getData()) {
                $user->addRole($role);
            }
        }
    }
}
