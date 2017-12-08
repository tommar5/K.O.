<?php

namespace AppBundle\Entity;

use AppBundle\Validator\Constraints as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Application
 *
 * @ORM\Entity(repositoryClass="ApplicationRepository")
 * @ORM\Table(name="applications")
 * @AppAssert\ApplicationClass
 */
class Application extends CompetitionInfo
{
    const STATUS_PAID                             = 'paid';
    const STATUS_NOT_PAID                         = 'invoice';
    const STATUS_DECLINED                         = 'declined';
    const STATUS_PRODUCED                         = 'produced';
    const STATUS_CONFIRMED                        = 'confirmed';
    const STATUS_CANCELLED                        = 'cancelled';
    const STATUS_UNCONFIRMED                      = 'unconfirmed';
    const STATUS_CONTRACT_UPLOADED_BY_LASF        = 'signed_application_by_lasf';
    const STATUS_CONTRACT_UPLOADED_BY_ORGANISATOR = 'signed_application_by_organisator';
    const STATUS_CONTRACT_BY_LASF_DELETED         = 'contract_by_lasf_deleted';
    const STATUS_CONTRACT_BY_ORGANISATOR_DELETED  = 'contract_by_organisator_deleted';
    const APPLICATION_COPY = 'application_copy';

    public static $applicationAgreementStatuses = [
        self::STATUS_NOT_PAID,
        self::STATUS_PAID,
        self::STATUS_CONFIRMED,
        self::STATUS_CONTRACT_UPLOADED_BY_LASF,
        self::STATUS_CONTRACT_UPLOADED_BY_ORGANISATOR,
        self::STATUS_CONTRACT_BY_LASF_DELETED,
        self::STATUS_CONTRACT_BY_ORGANISATOR_DELETED,
    ];

    public static $applicationInvoiceUploadStatuses = [
        self::STATUS_NOT_PAID,
        self::STATUS_PAID,
    ];

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="city", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $city;

    /**
     * @var string
     * @ORM\Column(name="street", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $street;

    /**
     * @var string
     * @ORM\Column(name="state", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $state;

    /**
     * @var string
     * @ORM\Column(name="country", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $country;

    /**
     * @var string
     * @ORM\Column(name="zip_code", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $zipCode;

    /**
     * @var string
     * @ORM\Column(name="street_number", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $streetNumber;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $lasfName;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $lasfAddress;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $lasfEmail;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Regex(pattern="/^\+?\d+$/", message="user.profile.incorrect_phone")
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $bank;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $bankAccount;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $vatNumber;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $memberCode;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $status = self::STATUS_UNCONFIRMED;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $deliverTo;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $deliverToAddress;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="applications")
     */
    private $user;

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="terms_confirmed")
     */
    private $termsConfirmed = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="svo_delegate_confirmed")
     */
    private $svoDelegateConfirmed = false;

    /**
     * @var string
     * @ORM\Column(name="reason", type="text", nullable=true)
     * @Assert\Length(max=16000)
     */
    private $reason;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $contractUrl;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="observerApplications")
     */
    private $observer;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="skkApplications")
     */
    private $skkHead;

    /**
     * @var FileUpload[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="FileUpload", cascade={"persist"})
     * @ORM\JoinTable(name="application_documents",
     *      joinColumns={@ORM\JoinColumn(name="application_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $documents;

    /**
     * @var ApplicationAgreement
     * @ORM\OneToOne(targetEntity="ApplicationAgreement", mappedBy="application")
     */
    private $applicationAgreement;

    /**
     * @var Sport
     * @ORM\ManyToOne(targetEntity="Sport", inversedBy="applications")
     * @Assert\NotBlank()
     */
    private $sport;

    /**
     * @var CompetitionChief
     * @ORM\ManyToOne(targetEntity="CompetitionChief", inversedBy="applications")
     */
    private $competitionChief;

    /**
     * @var User[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="application_judges",
     *      joinColumns={@ORM\JoinColumn(name="application_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private $judges;

    /**
     * @var SafetyChief
     * @ORM\ManyToOne(targetEntity="SafetyChief", inversedBy="applications")
     */
    private $safetyChief;


    /**
     * @var Steward[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="Steward", inversedBy="applications")
     * @ORM\JoinTable(name="application_stewards",
     *      joinColumns={@ORM\JoinColumn(name="application_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="steward_id", referencedColumnName="id")}
     * )
     */
    private $stewards;

    /**
     * @var SubCompetition[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="SubCompetition", mappedBy="application", cascade={"persist", "remove"})
     */
    private $subCompetitions;


    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->subCompetitions = new ArrayCollection();
        $this->judges = new ArrayCollection();
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
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * @param string $streetNumber
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @return string
     */
    public function getLasfName()
    {
        return $this->lasfName;
    }

    /**
     * @param string $lasfName
     */
    public function setLasfName($lasfName)
    {
        $this->lasfName = $lasfName;
    }

    /**
     * @return string
     */
    public function getLasfAddress()
    {
        return $this->lasfAddress;
    }

    /**
     * @param string $lasfEmail
     */
    public function setLasfEmail($lasfEmail)
    {
        $this->lasfEmail = $lasfEmail;
    }

    /**
     * @return string
     */
    public function getLasfEmail()
    {
        return $this->lasfEmail;
    }

    /**
     * @param string $lasfAddress
     */
    public function setLasfAddress($lasfAddress)
    {
        $this->lasfAddress = $lasfAddress;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param string $bank
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    /**
     * @return string
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * @param string $bankAccount
     */
    public function setBankAccount($bankAccount)
    {
        $this->bankAccount = $bankAccount;
    }

    /**
     * @return string
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }

    /**
     * @param string $vatNumber
     */
    public function setVatNumber($vatNumber)
    {
        $this->vatNumber = $vatNumber;
    }

    /**
     * @return string
     */
    public function getMemberCode()
    {
        return $this->memberCode;
    }

    /**
     * @param string $memberCode
     */
    public function setMemberCode($memberCode)
    {
        $this->memberCode = $memberCode;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function hasAgreementStatus()
    {
        return in_array($this->getStatus(), self::$applicationAgreementStatuses);
    }

    /**
     * @return bool
     */
    public function hasUploadInvoiceStatus()
    {
        return in_array($this->getStatus(), self::$applicationInvoiceUploadStatuses);
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param FileUpload $document
     */
    public function addDocument(FileUpload $document)
    {
        $this->documents->add($document);
    }

    public function removeDocument(FileUpload $document)
    {
        $this->documents->removeElement($document);
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
     * @return string
     */
    public function getDeliverTo()
    {
        return $this->deliverTo;
    }

    /**
     * @param string $deliverTo
     */
    public function setDeliverTo($deliverTo)
    {
        $this->deliverTo = $deliverTo;
    }

    /**
     * @return string
     */
    public function getDeliverToAddress()
    {
        return $this->deliverToAddress;
    }

    /**
     * @param string $deliverToAddress
     */
    public function setDeliverToAddress($deliverToAddress)
    {
        $this->deliverToAddress = $deliverToAddress;
    }

    /**
     * @return bool
     */
    public function isTermsConfirmed()
    {
        return $this->termsConfirmed;
    }

    /**
     * @param bool $termsConfirmed
     */
    public function setTermsConfirmed($termsConfirmed)
    {
        $this->termsConfirmed = $termsConfirmed;
    }

    /**
     * @return bool
     */
    public function isSvoDelegateConfirmed()
    {
        return $this->svoDelegateConfirmed;
    }

    /**
     * @param bool $svoDelegateConfirmed
     */
    public function setSvoDelegateConfirmed($svoDelegateConfirmed)
    {
        $this->svoDelegateConfirmed = $svoDelegateConfirmed;
    }

    /**
     * Get documents
     *
     * @return FileUpload[]|ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @return ApplicationAgreement
     */
    public function getApplicationAgreement()
    {
        return $this->applicationAgreement;
    }

    /**
     * @param ApplicationAgreement $applicationAgreement
     */
    public function setApplicationAgreement(ApplicationAgreement $applicationAgreement)
    {
        $this->applicationAgreement = $applicationAgreement;
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
    public function setSkkHead(User $skkHead = null)
    {
        $this->skkHead = $skkHead;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @return Sport
     */
    public function getSport()
    {
        return $this->sport;
    }

    /**
     * @param Sport|null $sport
     */
    public function setSport($sport)
    {
        $this->sport = $sport;
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
     * @param CompetitionChief $competitionChief
     */
    public function setCompetitionChief(CompetitionChief $competitionChief = null)
    {
        $this->competitionChief = $competitionChief;
    }

    /**
     * @return string
     */
    public function getContractUrl()
    {
        return $this->contractUrl;
    }

    /**
     * @param string $contractUrl
     */
    public function setContractUrl($contractUrl)
    {
        $this->contractUrl = $contractUrl;
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
     * @param User $judge
     */
    public function addJudge(User $judge)
    {
        $this->judges->add($judge);
    }

    public function removeJudge(User $judge)
    {
        $this->judges->removeElement($judge);
    }

    public function getJudges()
    {
        return $this->judges;
    }

    /**
     * @return Steward[]|ArrayCollection
     */
    public function getStewards()
    {
        return $this->stewards;
    }

    /**
     * @return SubCompetition[]|ArrayCollection
     */
    public function getSubCompetitions()
    {
        return $this->subCompetitions;
    }

    /**
     * @param SubCompetition[]|ArrayCollection $subCompetitions
     */
    public function setSubCompetitions($subCompetitions)
    {
        $this->subCompetitions = $subCompetitions;
    }

    /**
     * @param SubCompetition $subCompetition
     * @return Application
     */
    public function addSubCompetition(SubCompetition $subCompetition)
    {
        $subCompetition->setApplication($this);
        $this->subCompetitions->add($subCompetition);

        return $this;
    }

    /**
     * @param SubCompetition $subCompetition
     * @return Application
     */
    public function removeSubCompetition(SubCompetition $subCompetition)
    {
        if ($this->subCompetitions->contains($subCompetition)) {
            $this->subCompetitions->removeElement($subCompetition);
        }

        return $this;
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

    public function isPaid()
    {
        return $this->status == self::STATUS_PAID;
    }

    public function isNotPaid()
    {
        return $this->status == self::STATUS_NOT_PAID;
    }

    public function isCancelled()
    {
        return $this->status == self::STATUS_CANCELLED;
    }

    public function isUnconfirmed()
    {
        return $this->getStatus() == self::STATUS_UNCONFIRMED;
    }

    public function isConfirmed()
    {
        return $this->getStatus() == self::STATUS_CONFIRMED;
    }

    public function isDeclined()
    {
        return $this->getStatus() == self::STATUS_DECLINED;
    }

    public function isLasfUploadedContract()
    {
        return $this->getStatus() == self::STATUS_CONTRACT_UPLOADED_BY_LASF;
    }

    public function isOrganisatorUploadedContract()
    {
        return $this->getStatus() == self::STATUS_CONTRACT_UPLOADED_BY_ORGANISATOR;
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
    public function isDisabled() {

        $now = new \DateTime();
        if ($this->hasAgreementStatus() && $this->getDateTo()->format('Y-m-d') <= $now->format('Y-m-d')) {
            return true;
        }

        return false;
    }
}

