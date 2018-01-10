<?php

namespace AppBundle\Menu;

use AppBundle\Entity\User;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MenuBuilder extends ContainerAware
{
    /**
     * @param FactoryInterface $factory
     * @return \Knp\Menu\ItemInterface
     */
    public function top(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');

        if ($this->getUser() instanceof UserInterface) {
            $this->addUserMenus($menu, $this->getUser());
        } else {
            $this->addGuestMenus($menu);
        }

        return $menu;
    }

    private function addUserMenus(ItemInterface $menu, User $user)
    {
        $child = function ($label, $route) use ($menu) {
            $attributes = ['role' => 'presentation'];
            $menu->addChild($this->container->get('translator')->trans($label, [], 'menu'), compact('route', 'attributes'));
        };

        if ($this->userHasAnyOfRoles($user, ['ROLE_ADMIN'])) {
            $child('users', 'app_user_index');
        }

//        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_ACCOUNTANT')) {
//            $child('licences', 'app_licences_index');
//        }

        if ($this->userHasAnyOfRoles($user, ["ROLE_ORGANISATOR", "ROLE_DECLARANT", "ROLE_ADMIN", "ROLE_SECRETARY", "ROLE_ACCOUNTANT", "ROLE_LASF_COMMITTEE", "ROLE_SPECTATOR", "ROLE_SVO_COMMITTEE", "ROLE_COMPETITION_CHIEF", "ROLE_JUDGE_COMMITTEE", "ROLE_SKK_HEAD"])) {
            $child('applications', 'app_application_index');
        }

        if ($user->hasRole('ROLE_DEPARTMENT')) {
            $child('department', 'app_department_index');
        }

//        if ($user->hasRole('ROLE_DECLARANT')) {
//            $child('my_racers', 'app_user_racers');
//        }

//        if ($this->userHasAnyOfRoles($user, ["ROLE_RACER", "ROLE_DECLARANT", "ROLE_ORGANISATOR", "ROLE_JUDGE"])) {
//            $child('my_licences', 'app_licences_my');
//        }

        if ($user->hasRole('ROLE_ADMIN')) {
            // dropdown
            $dropdown = $menu->addChild($user->isLegal() ? $user->getMemberName() : $user->getFirstname(), [
                'attributes' => [
                    'role' => 'presentation',
                    'dropdown' => true,
                    'icon' => 'fa fa-user',
                ]
            ]);

            $dropdown->addChild($this->trans('files'), [
                'route' => 'app_fileuploads_index',
                'attributes' => ['icon' => 'fa fa-files-o']
            ]);

            $dropdown->addChild($this->trans('cms'), [
                'route' => 'app_cmsblock_index',
                'attributes' => ['icon' => 'fa fa-pencil-square-o']
            ]);

            $dropdown->addChild($this->trans('sport'), [
                'route' => 'app_musicstyle_index',
                'attributes' => ['icon' => 'fa fa-music']
            ]);

            $dropdown->addChild($this->trans('music'), [
                'route' => 'app_music_index',
                'attributes' => ['icon' => 'fa fa-play']
            ]);

            $dropdown->addChild($this->trans('audit'), [
                'route' => 'app_audit_index',
                'attributes' => ['icon' => 'fa fa-history']
            ]);

            $dropdown->addChild($this->trans('templates'), [
                'route' => 'app_mailtemplate_index',
                'attributes' => ['icon' => 'fa fa-envelope']
            ]);

            $dropdown->addChild($this->trans('profile'), [
                'route' => 'app_user_profile',
                'attributes' => ['icon' => 'fa fa-child']
            ]);

            $dropdown->addChild($this->trans('help'), [
                'route' => 'app_dashboard_help',
                'attributes' => ['icon' => 'fa fa-question']
            ]);

            // logout
            $dropdown->addChild($this->trans('logout'), [
                'route' => 'app_auth_logout',
                'attributes' => [
                    'role' => 'presentation',
                    'icon' => 'fa fa-sign-out',
                ]
            ]);
        } else {
            $menu->addChild($this->trans('profile'), [
                'route' => 'app_user_profile',
                'attributes' => ['icon' => 'fa fa-user',]
            ]);

            $menu->addChild($this->trans('help'), [
                'route' => 'app_dashboard_help',
                'attributes' => ['icon' => 'fa fa-question']
            ]);

            // logout
            $menu->addChild($this->trans('logout'), [
                'route' => 'app_auth_logout',
                'attributes' => [
                    'role' => 'presentation',
                    'icon' => 'fa fa-sign-out',
                ]
            ]);
        }
    }

    protected function addGuestMenus(ItemInterface $menu)
    {
        $menu->addChild($this->trans('article'), [
            'route' => 'app_article_index',
            'attributes' => [
                'role' => 'presentation',
            ]
        ]);

        $menu->addChild($this->trans('music'), [
            'route' => 'app_music_index',
            'attributes' => [
                'role' => 'presentation',
            ]
        ]);

        $menu->addChild($this->trans('login'), [
            'route' => 'app_auth_login',
            'attributes' => [
                'role' => 'presentation',
                'icon' => 'fa fa-sign-in',
            ]
        ]);

        $menu->addChild($this->trans('register'), [
            'route' => 'app_auth_register',
            'attributes' => [
                'role' => 'presentation',
                'icon' => 'fa fa-check-circle',
            ]
        ]);
    }

    /**
     * @return User
     */
    private function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        $token = $this->container->get('security.token_storage')->getToken();
        if (!$token instanceof TokenInterface) {
            return null;
        }

        return $token->getUser();
    }

    /**
     * @param string $label
     * @return string
     */
    private function trans($label)
    {
        return $this->container->get('translator')->trans($label, [], 'menu');
    }

    /**
     * @param User $user
     * @param $roles
     * @return bool
     */
    private function userHasAnyOfRoles(User $user, $roles)
    {
        return sizeof(array_intersect($user->getRoles(), $roles)) > 0;
    }
}
