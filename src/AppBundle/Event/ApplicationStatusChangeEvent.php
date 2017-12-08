<?php namespace AppBundle\Event;

use AppBundle\Entity\Application;
use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ApplicationStatusChangeEvent
 * @package AppBundle\Event
 */
class ApplicationStatusChangeEvent extends Event
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->application->getUser();
    }

    /**
     * ApplicationStatusChangeEvent constructor.
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->application->getStatus();
    }
}
