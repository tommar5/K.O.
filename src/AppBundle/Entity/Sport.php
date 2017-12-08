<?php namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Validator\Constraints as AppAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="sports")
 * @UniqueEntity("name")
 * @UniqueEntity("alias")
 */
class Sport
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
     * @ORM\OneToMany(targetEntity="Application", mappedBy="sport", cascade={"remove"})
     */
    private $applications;

    /**
     * @var CompetitionChief[]
     *
     * @ORM\ManyToMany(targetEntity="CompetitionChief", mappedBy="sports")
     */
    private $competitionChiefs;

    /**
     * @var Steward[]
     *
     * @ORM\ManyToMany(targetEntity="Steward", mappedBy="sports")
     */
    private $stewards;

    /**
     * @var Licence[]
     *
     * @ORM\ManyToMany(targetEntity="Licence", mappedBy="sports")
     */
    private $licences;

    /**
     * @var User[]
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="sports")
     */
    private $committes;

    /**
     * @var SubCompetition[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="SubCompetition", mappedBy="sport", cascade={"remove"})
     */
    private $subCompetitions;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
        $this->competitionChiefs = new ArrayCollection();
        $this->stewards = new ArrayCollection();
        $this->licences = new ArrayCollection();
        $this->committes = new ArrayCollection();
        $this->subCompetitions = new ArrayCollection();
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
     * @return Sport
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param \DateTime $updatedAt
     * @return Sport
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @param Application $application
     * @return Sport
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
     * @return Sport
     */
    public function addSubCompetition(SubCompetition $subCompetition)
    {
        $this->subCompetitions->add($subCompetition);

        return $this;
    }

    /**
     * @param SubCompetition $subCompetition
     */
    public function removeSubCompetition(SubCompetition $subCompetition)
    {
        $this->subCompetitions->removeElement($subCompetition);
    }

    /**
     * @return SubCompetition[]|ArrayCollection
     */
    public function getSubCompetitions()
    {
        return $this->subCompetitions;
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
     * @return Licence[]|ArrayCollection
     */
    public function getLicence()
    {
        return $this->licences;
    }

    /**
     * @param Licence $licences
     */
    public function addLicence(Licence $licences)
    {
        $this->licences->add($licences);
    }

    public function removeLicence(Licence $licences)
    {
        $this->licences->removeElement($licences);
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
