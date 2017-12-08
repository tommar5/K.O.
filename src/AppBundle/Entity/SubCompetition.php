<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Validator\Constraints as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table()
 * @AppAssert\ApplicationClass
 */
class SubCompetition extends CompetitionInfo
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Application
     * @ORM\ManyToOne(targetEntity="Application", inversedBy="subCompetitions")
     */
    private $application;

    /**
     * @var User[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="sub_competition_judges",
     *      joinColumns={@ORM\JoinColumn(name="sub_competition_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private $judges;

    /**
     * @var Steward[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="Steward", inversedBy="subCompetitions")
     * @ORM\JoinTable(name="sub_competition_stewards",
     *      joinColumns={@ORM\JoinColumn(name="sub_competition_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="steward_id", referencedColumnName="id")}
     * )
     */
    private $stewards;

    /**
     * @var SafetyChief
     * @ORM\ManyToOne(targetEntity="SafetyChief", inversedBy="subCompetition")
     */
    private $safetyChief;

    /**
     * @var CompetitionChief
     * @ORM\ManyToOne(targetEntity="CompetitionChief", inversedBy="subCompetition")
     */
    private $competitionChief;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="observerSubCompetitions")
     */
    private $observer;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="skkSubCompetitions")
     */
    private $skkHead;

    /**
     * @var Sport
     * @ORM\ManyToOne(targetEntity="Sport", inversedBy="subCompetitions")
     * @Assert\NotBlank()
     */
    private $sport;

    /**
     * @var FileUpload[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="FileUpload", cascade={"persist"})
     * @ORM\JoinTable(name="sub_competition_documents",
     *      joinColumns={@ORM\JoinColumn(name="sub_competition_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $documents;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->stewards = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return Sport
     */
    public function getSport()
    {
        return $this->sport;
    }

    /**
     * @param Sport $sport
     */
    public function setSport($sport)
    {
        $this->sport = $sport;
    }

    /**
     * @return string
     */
    public function getObserver()
    {
        return $this->observer;
    }

    /**
     * @param User|null $observer
     */
    public function setObserver(User $observer = null)
    {
        $this->observer = $observer;
    }

    /**
     * @param Steward $steward
     */
    public function addSteward(Steward $steward)
    {
        if (!$this->stewards->contains($steward)) {
            $this->stewards->add($steward);
        }
    }

    /**
     * @param Steward $steward
     */
    public function removeSteward(Steward $steward)
    {
        if ($this->stewards->contains($steward)) {
            $this->stewards->removeElement($steward);
        }
    }

    /**
     * @return Steward[]|ArrayCollection
     */
    public function getStewards()
    {
        return $this->stewards;
    }

    /**
     * @param User $judge
     */
    public function addJudge(User $judge)
    {
        $this->judges->add($judge);
    }

    /**
     * @param User $judge
     */
    public function removeJudge(User $judge)
    {
        $this->judges->removeElement($judge);
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getJudges()
    {
        return $this->judges;
    }

    /**
     * @return SafetyChief
     */
    public function getSafetyChief()
    {
        return $this->safetyChief;
    }

    /**
     * @param SafetyChief $safetyChief
     */
    public function setSafetyChief(SafetyChief $safetyChief = null)
    {
        $this->safetyChief = $safetyChief;
    }

    /**
     * @return CompetitionChief
     */
    public function getCompetitionChief()
    {
        return $this->competitionChief;
    }

    /**
     * @param CompetitionChief $competitionChief
     */
    public function setCompetitionChief(CompetitionChief $competitionChief = null)
    {
        $this->competitionChief = $competitionChief;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isCompetitionChief(User $user)
    {
        if ($this->getCompetitionChief()) {
            return $this->getCompetitionChief()->getUser() == $user;
        }

        return false;
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
     * @return User
     */
    public function getSkkHead()
    {
        return $this->skkHead;
    }

    /**
     * @param User $skkHead
     */
    public function setSkkHead(User $skkHead)
    {
        $this->skkHead = $skkHead;
    }

    /**
     * Helper Function to get ArrayCollection of documents by Type
     * @param $type
     * @return bool
     */
    public function getDocumentsByType($type)
    {
        return array_key_exists($type, $this->getAllTypeDocuments());
    }

    /**
     * Helper Function to get array of documents by Type
     *
     * @return array
     */
    public function getAllTypeDocuments()
    {
        $documentsByType = [];
        foreach ($this->getDocuments() as $document) {
            $documentsByType[$document->getType()][] = $document;
        }

        return $documentsByType;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        $now = new \DateTime();
        if ($this->getApplication()->hasAgreementStatus() && $this->getDateTo()->format('Y-m-d') <= $now->format('Y-m-d')) {
            return true;
        }

        return false;
    }
}
