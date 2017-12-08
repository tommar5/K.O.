<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;

/**
 * Document approvals
 *
 * @ORM\Table(name="approval")
 * @ORM\Entity
 */
class Approval
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @var FileUpload
     * @ORM\ManyToOne(targetEntity="FileUpload", inversedBy="approvals")
     * @ORM\JoinColumn(name="file_upload_id", referencedColumnName="id", nullable=false)
     */
    private $document;

    /**
     * @var \DateTime
     * @ORM\Column(name="createdAt", type="datetime")
     * @Assert\DateTime()
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return FileUpload
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param FileUpload $document
     */
    public function setDocument(FileUpload $document)
    {
        $this->document = $document;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
