<?php namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Validator\Constraints as AppAssert;

/**
 * @ORM\Entity
 * @ORM\Table()
 * @AppAssert\CompetitionClass
 */
class Competition
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_from", type="datetime")
     * @Assert\NotBlank(message="competition.date_from")
     * @Assert\Date()
     */
    private $dateFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_to", type="datetime", nullable=true)
     * @Assert\Date()
     */
    private $dateTo;

    /**
     * @var string
     *
     * @ORM\Column(name="location", length=255)
     * @Assert\NotBlank(message="competition.location")
     * @Assert\Length(max=255)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="name", length=255)
     * @Assert\NotBlank(message="competition.name")
     * @Assert\Length(max=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $watcher;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, name="safety_watcher", nullable=true)
     * @Assert\Length(max=255)
     */
    private $safetyWatcher;

    /**
     * @ORM\OneToMany(targetEntity="CompetitionJudge", mappedBy="competition", cascade={"remove"})
     */
    private $judges;

    /**
     * @var CompetitionParticipant[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="CompetitionParticipant", mappedBy="competition", cascade={"remove"})
     */
    private $participants;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="leadedCompetitions")
     */
    private $mainJudge;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="organizedCompetitions")
     */
    private $user;

    /**
     * @var Licence
     * @ORM\ManyToOne(targetEntity="Licence")
     */
    private $licence;

    /**
     * @var FileUpload[]
     * @ORM\ManyToMany(targetEntity="FileUpload")
     */
    private $documents;

    public function __construct()
    {
        $this->judges = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->participants = new ArrayCollection();
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
     * @return \DateTime
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @param \DateTime $dateFrom
     */
    public function setDateFrom(\DateTime $dateFrom = null)
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return \DateTime
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @param \DateTime|null $dateTo
     */
    public function setDateTo(\DateTime $dateTo = null)
    {
        $this->dateTo = $dateTo;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param \DateTime $createdAt
     * @return Competition
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param \DateTime $updatedAt
     * @return Competition
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @param CompetitionJudge $judge
     * @return Competition
     */
    public function addJudge(CompetitionJudge $judge)
    {
        $this->judges->add($judge);

        return $this;
    }

    /**
     * @param CompetitionJudge $judge
     */
    public function removeJudge(CompetitionJudge $judge)
    {
        $this->judges->removeElement($judge);
    }

    /**
     * @return User[]
     */
    public function getJudges()
    {
        return $this->judges;
    }

    /**
     * @param User $mainJudge
     * @return Competition
     */
    public function setMainJudge(User $mainJudge = null)
    {
        $this->mainJudge = $mainJudge;

        return $this;
    }

    /**
     * @return User
     */
    public function getMainJudge()
    {
        return $this->mainJudge;
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

    public function isOwner(User $user)
    {
        return $this->getUser()->getId() == $user->getId();
    }

    /**
     * @return string
     */
    public function getWatcher()
    {
        return $this->watcher;
    }

    /**
     * @param string $watcher
     */
    public function setWatcher($watcher)
    {
        $this->watcher = $watcher;
    }

    /**
     * @return string
     */
    public function getSafetyWatcher()
    {
        return $this->safetyWatcher;
    }

    /**
     * @param string $safetyWatcher
     */
    public function setSafetyWatcher($safetyWatcher)
    {
        $this->safetyWatcher = $safetyWatcher;
    }

    /**
     * @return Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * @param Licence $licence
     */
    public function setLicence(Licence $licence)
    {
        $this->licence = $licence;
    }

    /**
     * @return FileUpload[]|ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @param FileUpload $document
     */
    public function addDocument(FileUpload $document)
    {
        $this->documents->add($document);
    }

    /**
     * @param FileUpload $document
     */
    public function removeDocument(FileUpload $document)
    {
        $this->documents->removeElement($document);
    }

    /**
     * @return CompetitionParticipant[]
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param CompetitionParticipant $participant
     */
    public function addParticipant(CompetitionParticipant $participant)
    {
        $participant->setCompetition($this);
        $this->participants->add($participant);
    }

    /**
     * @param CompetitionParticipant $participant
     */
    public function removeParticipant(CompetitionParticipant $participant)
    {
        $this->participants->removeElement($participant);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function userParticipates(User $user)
    {
        foreach ($this->participants as $part) {
            if ($part->getUser()->getId() == $user->getId()) {
                return true;
            }
        }

        return false;
    }
}
