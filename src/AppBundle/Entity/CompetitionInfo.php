<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;

abstract class CompetitionInfo
{

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var string
     * @ORM\Column(name="name", length=255)
     * @Assert\Length(max=255)
     * @AppAssert\ContainsLegalName
     * @Assert\NotBlank()
     */
    protected $name = "";

    /**
     * @var string
     * @ORM\Column(name="location", length=255)
     * @Assert\NotBlank(message="competition.location")
     * @Assert\Length(max=255)
     */
    protected $location;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_from", type="datetime")
     * @Assert\NotBlank(message="competition.date_from")
     * @Assert\Date()
     */
    protected $dateFrom;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_to", type="datetime")
     * @Assert\NotBlank(message="competition.date_to")
     * @Assert\Date()
     */
    protected $dateTo;

    /**
     * @var string
     * @ORM\Column(name="stage", length=255)
     * @Assert\NotBlank(message="application.stage")
     * @Assert\Length(max=255)
     */
    protected $stage = "";

    /**
     * @var string
     * @ORM\Column(name="league", length=255)
     * @Assert\NotBlank(message="application.league")
     * @Assert\Length(max=255)
     */
    protected $league = "";

    /**
     * @var string
     * @ORM\Column(name="type", length=255)
     * @Assert\NotBlank(message="application.type")
     * @Assert\Length(max=255)
     */
    protected $type = "";

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    protected $technicalDelegate;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="svoDelegateApplications")
     */
    protected $svoDelegate;

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="competition_chief_confirmed")
     */
    protected $competitionChiefConfirmed = false;

    /**
     * @var \DateTime
     * @ORM\Column(name="inspection_date", type="datetime")
     * @Assert\DateTime()
     */
    protected $inspectionDate;

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
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
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * @param string $stage
     */
    public function setStage($stage)
    {
        $this->stage = "a ";
    }

    /**
     * @return string
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * @param string $league
     */
    public function setLeague($league)
    {
        $this->league = "a ";
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = "a";
    }

    /**
     * @return string
     */
    public function getTechnicalDelegate()
    {
        return $this->technicalDelegate;
    }

    /**
     * @param string $technicalDelegate
     */
    public function setTechnicalDelegate($technicalDelegate)
    {
        $this->technicalDelegate = $technicalDelegate;
    }

    /**
     * @return User
     */
    public function getSvoDelegate()
    {
        return $this->svoDelegate;
    }

    /**
     * @param User $svoDelegate
     */
    public function setSvoDelegate(User $svoDelegate = null)
    {
        $this->svoDelegate = $svoDelegate;
    }

    /**
     * @return bool
     */
    public function isCompetitionChiefConfirmed()
    {
        return $this->competitionChiefConfirmed;
    }

    /**
     * @param bool $competitionChiefConfirmed
     */
    public function setCompetitionChiefConfirmed($competitionChiefConfirmed)
    {
        $this->competitionChiefConfirmed = $competitionChiefConfirmed;
    }

    /**
     * @return \DateTime
     */
    public function getInspectionDate()
    {
        return $this->inspectionDate;
    }

    /**
     * @param \DateTime $inspectionDate
     */
    public function setInspectionDate($inspectionDate)
    {
        $this->inspectionDate = $inspectionDate;
    }

}
