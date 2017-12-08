<?php namespace AppBundle\EventListener;

use AppBundle\Event\UserCreateEvent;
use AppBundle\Mailer\Mailer;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class UserCreateListener
 * @package AppBundle\EventListener
 */
class UserCreateListener
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Router
     */
    private $router;
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * CreateRacerListener constructor.
     * @param Mailer|\Swift_Mailer $mailer
     * @param Router $router
     * @param EntityManager $em
     * @param TranslatorInterface $translator
     */
    public function __construct(Mailer $mailer, Router $router, EntityManager $em, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * Send email to newly created racer
     * @param UserCreateEvent $event
     */
    public function notifyUser(UserCreateEvent $event)
    {
        $user = $event->getUser();

        $this->mailer->user($user, 'new_user', $this->getEmailData($user));
    }

    /**
     * Send email to declarant about newly created racer
     * @param UserCreateEvent $event
     */
    public function notifyDeclarant(UserCreateEvent $event)
    {
        $user = $event->getUser();
        $parent = $user->getParent();

        if ($user->hasRole('ROLE_RACER') && $parent) {
            $this->mailer->user($parent, 'declarant_notify_new_user', $this->getEmailData($user));
        }
    }

    /**
     * Send email to admin about newly created user
     * @param UserCreateEvent $event
     */
    public function notifyAdmin(UserCreateEvent $event)
    {
        /** @var User[] $admins */
        $admins = $this->em->getRepository(User::class)->createQueryBuilder('u')
            ->where('BIT_AND(u.roles, :role) > 0')
            ->setParameter('role', User::$roleMap['ROLE_ADMIN'])
            ->getQuery()
            ->getResult();

        foreach ($admins as $admin) {
            $this->mailer->user($admin, 'admin_notify_new_user', $this->getEmailData($event->getUser()));
        }
    }

    /**
     * @param User $user
     * @return array
     */
    private function getEmailData(User $user)
    {
        $roles = [];

        // Translate user roles
        foreach ($user->getRoles() as $role) {
            $roles[] = $this->translator->trans(User::toTranslation($role));
        }

        return [
            'full_name' => $user->getFullName(),
            'user_role' => implode(', ', $roles),
            'username' => $user->getUsername(),
            'password' => $user->getPlainPassword(),
            'link' => $this->router->generate('app_auth_login', array(), true),
        ];
    }
}
