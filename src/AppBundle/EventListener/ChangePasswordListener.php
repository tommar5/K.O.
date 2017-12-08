<?php namespace AppBundle\EventListener;

use AppBundle\Controller\ChangePasswordInterface;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChangePasswordListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Router
     */
    private $router;

    public function __construct(TokenStorageInterface $tokenStorage, Router $router)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        if (!$controller[0] instanceof ChangePasswordInterface) {
            return;
        }

        $userToken = $this->tokenStorage->getToken();
        if ($userToken && $user = $userToken->getUser()) {
            if ($user instanceof User && $user->getChangePassword()) {
                $redirectUrl = $this->router->generate('app_auth_changepassword');
                $event->setController(function() use ($redirectUrl) {
                    return new RedirectResponse($redirectUrl);
                });
            }
        }
    }
}
