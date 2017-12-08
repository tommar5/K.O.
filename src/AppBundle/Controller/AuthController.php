<?php namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Event\UserCreateEvent;
use AppBundle\Form\Type\User\ChangePasswordType;
use AppBundle\Form\Type\User\ConfirmType;
use AppBundle\Form\Type\User\ResetType;
use AppBundle\Form\Type\User\SignupType;
use AppBundle\Form\Type\User\TermsConfirmType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;

class AuthController extends Controller
{
    use DoctrineController;

    /**
     * @Route("/login")
     * @Method("GET")
     * @Template
     */
    public function loginAction()
    {
        /** @var \Symfony\Component\Security\Http\Authentication\AuthenticationUtils $authenticationUtils */
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            if ($error instanceof DisabledException) {
                $error = $this->get('translator')->trans('user.login.account_disabled');
            } elseif ($error instanceof LockedException) {
                $error = $this->get('translator')->trans('user.login.account_locked');
            } elseif ($error->getMessageKey() === 'Invalid credentials.') {
                $error = $this->get('translator')->trans('user.login.incorrect_credentials');
            }
        }
        return compact('lastUsername', 'error');
    }

    /**
     * @Route("/register")
     * @Method({"GET", "POST"})
     * @Template
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(new SignupType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $user->setRoles([]);
            foreach (User::$roleMap as $role => $roleValue) {
                if (!$form->has($role)) {
                    continue;
                }

                if ($form->get($role)->getData()) {
                    $user->addRole($role);
                }
            }

            $user->setConfirmed(false);
            $user->setChangePassword(true);
            $reg = $this->get('app.registrator');
            $user->setPlainPassword($reg->createRandomString(8));
            $reg->registerUser($user, $user);
            $this->persist($user);

            $this->flush();

            $this->addFlash('success', $this->get('translator')->trans('user.flash.registered'));

            $this->get('event_dispatcher')->dispatch('user.created', new UserCreateEvent($user));

            return $this->redirect($this->generateUrl('app_auth_login'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/confirm/{token}")
     * @Method({"GET", "POST"})
     * @ParamConverter("user", class="AppBundle:User")
     * @Template
     * @param Request $request
     * @param User $user
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmAction(Request $request, User $user)
    {
        $form = $this->createForm(new ConfirmType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user->setChangePassword(false);
            $this->get('app.registrator')->updateUser($user);
            $user->setToken(null);
            $this->flush();

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);

            $this->addFlash('success', $this->get('translator')->trans('user.flash.password_changed'));

            return $this->redirect($this->generateUrl('app_user_profile'));
        }

        return ['form' => $form->createView(), 'token' => $user->getToken()];
    }

    /**
     * @Route("/change-password")
     * @Method({"GET", "POST"})
     * @Template
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changePasswordAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->getChangePassword()) {
            return $this->redirectToRoute('app_user_profile');
        }

        $form = $this->createForm(new ChangePasswordType($user), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user->setChangePassword(false);
            $this->get('app.registrator')->updateUser($user);
            $this->flush();

            $this->addFlash('success', $this->get('translator')->trans('user.flash.password_changed'));

            return $this->redirect($this->generateUrl('app_dashboard_help'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/reset")
     * @Method({"GET", "POST"})
     * @Template
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function resetAction(Request $request)
    {
        $form = $this->createForm(new ResetType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $email = $form->get('email')->getData();

            /** @var User $user */
            if ($user = $this->get('em')->getRepository(User::class)->findOneBy(['email' => $email])) {
                $user->regenerateToken();
                $this->persist($user);
                $this->flush();

                $this->get('mail')->user($user, 'remind_password', [
                    'link' => $this->generateUrl('app_auth_confirm', ['token' => $user->getToken()], true),
                ]);

                $this->addFlash('success', $this->get('translator')->trans('user.flash.remind_sent', [
                    '%email%' => $user->getEmail()
                ]));

                return $this->redirect($this->generateUrl('app_auth_login'));
            }

            $form->get('email')->addError(new FormError($this->get('translator')->trans('user.reset.user_not_found')));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/agree-with-terms")
     * @Method({"GET", "POST"})
     * @Template
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function termsAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(new TermsConfirmType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->flush();

            if ($user->isTermsConfirmed()) {
                $this->addFlash('success', $this->get('translator')->trans('user.flash.terms_agreed'));
            } else {
                $this->addFlash('info', $this->get('translator')->trans('user.flash.please_agree'));
            }

            return $this->redirect($this->generateUrl('app_dashboard_help'));
        }

        return ['form' => $form->createView()];
    }
}
