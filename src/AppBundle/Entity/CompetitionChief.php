<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;

/**
 * CompetitionChief
 *
 * @ORM\Entity
 * @ORM\Table(name="competition_chiefs")
 */
class CompetitionChief
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
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="competitionChiefs")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var Sport[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Sport", cascade={"persist"}, inversedBy="competitionChiefs")
     * @ORM\JoinTable(name="competition_chiefs_sports",
     *      joinColumns={@ORM\JoinColumn(name="competition_chief_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sport_id", referencedColumnName="id")}
     * )
     */
    private $sports;

    /**
     * @var Application[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Application", mappedBy="competitionChief")
     */
    private $applications;

    /**
     * @var SubCompetition[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="SubCompetition", mappedBy="competitionChief")
     */
    private $subCompetition;

    public function __construct()
    {
        $this->sports = new ArrayCollection();
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

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param Sport $sport
     */
    public function addSport(Sport $sport)
    {
        $this->sports->add($sport);
    }

    public function removeSport(Sport $sport)
    {
        $this->sports->removeElement($sport);
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
     * Get sports
     *
     * @return Sport[]|ArrayCollection
     */
    public function getSports()
    {
        return $this->sports;
    }

    /**
     * @param Application $application
     * @return CompetitionChief
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
     * @return CompetitionChief
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

