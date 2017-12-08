<?php namespace AppBundle\Event;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\Application;
use AppBundle\Entity\SubCompetition;
use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class UserCreateEvent
 * @package AppBundle\Event
 */
class DocumentStatusChangeEvent extends Event
{

    /**
     * @var Application
     */
    private $application;

    /**
     * @var FileUpload
     */
    private $fileUpload;

    /**
     * @var SubCompetition
     */
    private $competition;

    /**
     * UserCreateEvent constructor.
     * @param Application $application
     * @param FileUpload $fileUpload
     * @param SubCompetition $competition
     */
    public function __construct(Application $application, FileUpload $fileUpload = null, SubCompetition $competition = null)
    {
        $this->application = $application;
        $this->fileUpload = $fileUpload;
        $this->competition = $competition;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->application->getUser();
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->fileUpload->getStatus();
    }

    /**
     * @return string
     */
    public function getApplicationType()
    {
        return $this->application->getType();
    }

    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->fileUpload->getType();
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->fileUpload->getComment();
    }

    public function getCompetition()
    {
        return $this->competition;
    }

    public function getCompetitionType()
    {
        $this->competition->getType();
    }
}
