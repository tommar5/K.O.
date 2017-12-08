<?php namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
class DeclarantRequest
{
    const STATUS_WAITING = 'waiting';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_REJECTED = 'rejected';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $racer;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $currentDeclarant;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $newDeclarant;

    /**
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(name="status", type="text")
     */
    private $status;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param User $currentDeclarant
     *
     * @return DeclarantRequest
     */
    public function setCurrentDeclarant(User $currentDeclarant = null)
    {
        $this->currentDeclarant = $currentDeclarant;

        return $this;
    }

    /**
     * @return User
     */
    public function getCurrentDeclarant()
    {
        return $this->currentDeclarant;
    }

    /**
     * @param User $newDeclarant
     *
     * @return DeclarantRequest
     */
    public function setNewDeclarant(User $newDeclarant)
    {
        $this->newDeclarant = $newDeclarant;

        return $this;
    }

    /**
     * @return User
     */
    public function getNewDeclarant()
    {
        return $this->newDeclarant;
    }

    /**
     * @param string $comment
     *
     * @return DeclarantRequest
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param User $racer
     *
     * @return DeclarantRequest
     */
    public function setRacer(User $racer)
    {
        $this->racer = $racer;

        return $this;
    }

    /**
     * @return User
     */
    public function getRacer()
    {
        return $this->racer;
    }

    /**
     * @param string $status
     *
     * @return DeclarantRequest
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
