<?php namespace AppBundle\Event;

use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class UserCreateEvent
 * @package AppBundle\Event
 */
class UserCreateEvent extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * UserCreateEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
