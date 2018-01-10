<?php namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Validator\Constraints as AppAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="music_styles")
 * @UniqueEntity("name")
 * @UniqueEntity("alias")
 */
class MusicStyle
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
     * @var string
     *
     * @ORM\Column(name="name", length=255, unique=true)
     * @Assert\NotBlank(message="sport.name")
     * @Assert\Length(max=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", length=255, unique=true)
     * @Assert\NotBlank(message="sport.alias")
     * @Assert\Length(max=255)
     */
    private $alias;

    /**
     * @var Application[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Application", mappedBy="musicStyle", cascade={"remove"})
     */
    private $applications;

    /**
     * @var CompetitionChief[]
     *
     * @ORM\ManyToMany(targetEntity="CompetitionChief", mappedBy="musicStyles")
     */
    private $competitionChiefs;

    /**
     * @var Steward[]
     *
     * @ORM\ManyToMany(targetEntity="Steward", mappedBy="musicStyles")
     */
    private $stewards;

    /**
     * @var User[]
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="musicStyles")
     */
    private $committes;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
        $this->competitionChiefs = new ArrayCollection();
        $this->stewards = new ArrayCollection();
        $this->committes = new ArrayCollection();
    }

    /**
     * @return int
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @param \DateTime $createdAt
     * @return MusicStyle
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param \DateTime $updatedAt
     * @return MusicStyle
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @param Application $application
     * @return MusicStyle
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
     * @return CompetitionChief[]|ArrayCollection
     */
    public function getCompetitionChiefs()
    {
        return $this->competitionChiefs;
    }

    /**
     * @param CompetitionChief $competitionChief
     */
    public function addCompetitionChief(CompetitionChief $competitionChief)
    {
        $this->competitionChiefs->add($competitionChief);
    }

    public function removeCompetitionChief(CompetitionChief $competitionChief)
    {
        $this->competitionChiefs->removeElement($competitionChief);
    }

    /**
     * @return Steward[]|ArrayCollection
     */
    public function getStewards()
    {
        return $this->stewards;
    }

    /**
     * @param Steward $steward
     */
    public function addSteward(Steward $steward)
    {
        $this->stewards->add($steward);
    }

    public function removeSteward(Steward $steward)
    {
        $this->stewards->removeElement($steward);
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getCommittee()
    {
        return $this->committes;
    }

    /**
     * @param User $committee
     */
    public function addCommittee(User $committee)
    {
        $this->committes->add($committee);
    }

    /**
     * @param User $committee
     */
    public function removeCommittee(User $committee)
    {
        $this->committes->removeElement($committee);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
