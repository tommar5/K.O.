<?php namespace AppBundle\Event;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class UserCreateEvent
 * @package AppBundle\Event
 */
class StatusChangeEvent extends Event
{

    /**
     * @var Licence
     */
    private $licence;

    /**
     * @var FileUpload
     */
    private $fileUpload;

    /**
     * UserCreateEvent constructor.
     * @param Licence $licence
     * @param FileUpload $fileUpload
     */
    public function __construct(Licence $licence, FileUpload $fileUpload = null)
    {
        $this->licence = $licence;
        $this->fileUpload = $fileUpload;
    }

    /**
     * @return Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->licence->getUser();
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->licence->getStatus();
    }

    /**
     * @return string
     */
    public function getLicenceType()
    {
        return $this->licence->getType();
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
    public function getFileStatus()
    {
        return $this->fileUpload->getStatus();
    }
}
