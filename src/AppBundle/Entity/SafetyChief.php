<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;

/**
 * SafetyChief
 *
 * @ORM\Table(name="safety_chief")
 * @ORM\Entity
 */
class SafetyChief
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(length=64, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(length=64, nullable=true)
     */
    private $lastname;

    /**
     * @var Application[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Application", mappedBy="safetyChief")
     */
    private $applications;

    /**
     * @var SubCompetition[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="SubCompetition", mappedBy="safetyChief")
     */
    private $subCompetition;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
        $this->subCompetition = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }

    /**
     * @param Application $application
     * @return SafetyChief
     */
    public function addApplication(Application $application)
    {
        $this->applications->add($application);

        return $this;
    }

    /**
     * @param Application $application
     */
    public function removeApplication(Application $application)
    {
        $this->applications->removeElement($application);
    }

    /**
     * @return Application[]
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @param SubCompetition $subCompetition
     * @return SafetyChief
     */
    public function addSubCompetition(SubCompetition $subCompetition)
    {
        $this->subCompetition->add($subCompetition);

        return $this;
    }

    /**
     * @param SubCompetition $subCompetition
     */
    public function removeSubCompetition(SubCompetition $subCompetition)
    {
        $this->subCompetition->removeElement($subCompetition);
    }

    /**
     * @return SubCompetition[]|ArrayCollection
     */
    public function getSubCompetitions()
    {
        return $this->subCompetition;
    }
}

