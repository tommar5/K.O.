<?php namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="LicenceRepository")
 * @ORM\Table(name="licences")
 * @UniqueEntity(fields = {"series", "serialNumber"}, errorPath="serialNumber")
 */
class Licence extends UserInfo
{

    const DECLARANT_TYPE = 'declarant';
    const DRIVER_TYPE = 'driver';
    const JUDGE_TYPE = 'judge';
    const TRACK_TYPE = 'track';
    const MEMBERSHIP_TYPE = 'membership';
    const SAFETY_TYPE = 'safety';
    const ORGANISATOR_TYPE = 'organisator';

    const TYPE_DRIVER_M = 'driver_licence.m';
    const TYPE_DRIVER_E = 'driver_licence.e';
    const TYPE_DRIVER_JRE = 'driver_licence.jre';
    const TYPE_DRIVER_JRD = 'driver_licence.jrd';
    const TYPE_DRIVER_D = 'driver_licence.d';
    const TYPE_DRIVER_D2 = 'driver_licence.d2';
    const TYPE_DRIVER_B = 'driver_licence.b';
    const TYPE_DRIVER_C = 'driver_licence.c';
    const TYPE_DRIVER_R = 'driver_licence.r';

    const TYPE_JUDGE_FIRST = 'judge_licence.first';
    const TYPE_JUDGE_SECOND = 'judge_licence.second';
    const TYPE_JUDGE_THIRD = 'judge_licence.third';
    const TYPE_JUDGE_NATIONAL = 'judge_licence.national';
    const TYPE_JUDGE_INTERNATIONAL = 'judge_licence.international';
    const TYPE_JUDGE_TRAINEE = 'judge_licence.trainee';

    const TYPE_DECLARANT_A = 'declarant_licence.a';
    const TYPE_DECLARANT_B = 'declarant_licence.b';
    const TYPE_DECLARANT_K = 'declarant_licence.k';
    const TYPE_ORGANISATOR = 'organisator_licence';
    const TYPE_TRACK = 'track_licence';
    const TYPE_SAFETY = 'safety_licence';
    const TYPE_MEMBERSHIP = 'membership';

    const STATUS_UNCONFIRMED = 'unconfirmed';
    const STATUS_UPLOADED = 'uploaded';
    const STATUS_WAITING_EDIT = 'waiting_edit';
    const STATUS_WAITING_CONFIRM = 'waiting_confirm';
    const STATUS_NOT_PAID = 'not_paid';
    const STATUS_PAID = 'paid';
    const STATUS_EXTEND = 'extend';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DECLINED = 'declined';
    const STATUS_PRODUCED = 'produced';
    const STATUS_INVOICE = 'invoice';

    const FIRST_DRIVER = "firstDriver";
    const SECOND_DRIVER = "secondDriver";

    const STATUS_DOCUMENT_REJECTED = 'rejected';
    const STATUS_DOCUMENT_NEW = 'new';
    const STATUS_DOCUMENT_APPROVED = 'approved';

    const URGENCY_STANDARD = 'standard';
    const URGENCY_URGENT = 'urgent';

    public static $declarantTypes = [
        self::TYPE_DECLARANT_A,
        self::TYPE_DECLARANT_B,
        self::TYPE_DECLARANT_K,
    ];

    public static $driverTypes = [
        self::TYPE_DRIVER_B,
        self::TYPE_DRIVER_C,
        self::TYPE_DRIVER_D,
        self::TYPE_DRIVER_D2,
        self::TYPE_DRIVER_E,
        self::TYPE_DRIVER_JRE,
        self::TYPE_DRIVER_JRD,
        self::TYPE_DRIVER_M,
        self::TYPE_DRIVER_R,
    ];

    public static $judgeTypes = [
        self::TYPE_JUDGE_FIRST,
        self::TYPE_JUDGE_TRAINEE,
        self::TYPE_JUDGE_THIRD,
        self::TYPE_JUDGE_SECOND,
        self::TYPE_JUDGE_NATIONAL,
        self::TYPE_JUDGE_INTERNATIONAL,
    ];

    public static $organisatorTypes = [
        self::TYPE_ORGANISATOR,
    ];

    public static $completedStatuses = [
        self::STATUS_PAID,
        self::STATUS_PRODUCED,
    ];

    public static $extendableStatuses = [
        self::STATUS_PRODUCED,
        self::STATUS_PAID,
        self::STATUS_NOT_PAID,
        self::STATUS_INVOICE,
    ];

    public static $visibleStatuses = [
        self::STATUS_UPLOADED,
        self::STATUS_WAITING_EDIT,
        self::STATUS_WAITING_CONFIRM,
        self::STATUS_NOT_PAID,
        self::STATUS_PAID,
        self::STATUS_EXTEND,
        self::STATUS_CANCELLED,
        self::STATUS_PRODUCED,
        self::STATUS_INVOICE,
    ];

    public static $seriesNames = [
        self::TYPE_DRIVER_M => 'M',
        self::TYPE_DRIVER_E => 'E',
        self::TYPE_DRIVER_JRE => 'EJ',
        self::TYPE_DRIVER_JRD => 'DJ',
        self::TYPE_DRIVER_D => 'D',
        self::TYPE_DRIVER_D2 => 'D2',
        self::TYPE_DRIVER_B => 'B',
        self::TYPE_DRIVER_C => 'C',
        self::TYPE_DRIVER_R => 'R',

        self::TYPE_JUDGE_FIRST => 'T',
        self::TYPE_JUDGE_SECOND => 'T',
        self::TYPE_JUDGE_THIRD => 'T',
        self::TYPE_JUDGE_NATIONAL => 'T',
        self::TYPE_JUDGE_INTERNATIONAL => 'T',
        self::TYPE_JUDGE_TRAINEE => 'T',

        self::TYPE_DECLARANT_A => 'A',
        self::TYPE_DECLARANT_B => 'B',
        self::TYPE_DECLARANT_K => 'K',

        self::TYPE_ORGANISATOR => 'VOL',

        self::TYPE_TRACK => 'S',
        self::TYPE_SAFETY => '',
        self::TYPE_MEMBERSHIP => 'MEM',
    ];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", name="updated_at")
     */
    private $updatedAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="licences")
     */
    private $user;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", name="expires_at", nullable=true)
     */
    private $expiresAt;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $type;

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
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $status = self::STATUS_UNCONFIRMED;

    /**
     * @var FileUpload[]
     * @ORM\ManyToMany(targetEntity="FileUpload")
     */
    private $documents;

    /**
     * This is used by JUDGE licences only
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $declarant;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true, unique=true)
     */
    private $serialNumber;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $extending = false;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * @Assert\Length(max=255)
     */
    private $series;

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
    private $teamName;

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
    private $personalCode;

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
    private $mobileNumber;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $comment;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $managerFullName;

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
    private $name;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", length=255, nullable=true)
     */
    private $date;

    /**
     * @var Licence
     * @ORM\ManyToOne(targetEntity="Licence", inversedBy="basedOnLicence")
     */
    private $licence;

    /**
     * @var Licence
     * @ORM\OneToMany(targetEntity="Licence", mappedBy="licence")
     */
    private $basedOnLicence;

    /**
     * @var TeamRepresentative[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="TeamRepresentative", mappedBy="licence")
     */
    private $representatives;

    /**
     * @var string
     * @ORM\Column(name="reason", type="text", nullable=true)
     * @Assert\Length(max=16000)
     */
    private $reason;

    /**
     * @var bool
     * @ORM\Column(name="first_driver", type="boolean", nullable=true)
     */
    private $firstDriver;

    /**
     * @var bool
     * @ORM\Column(name="second_driver", type="boolean", nullable=true)
     */
    private $secondDriver;

    /**
     * @var Sport[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Sport", cascade={"persist"}, inversedBy="licences")
     * @ORM\JoinTable(name="licences_sports",
     *      joinColumns={@ORM\JoinColumn(name="licence_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sport_id", referencedColumnName="id")}
     * )
     */
    private $sports;

    /**
     * @var Language[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Language", cascade={"persist"}, inversedBy="licences")
     * @ORM\JoinTable(name="licence_languages",
     *      joinColumns={@ORM\JoinColumn(name="licence_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="id")}
     * )
     */
    private $languages;

    /**
     * @var string
     * @ORM\Column(name="identity_code", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $identityCode;

    /**
     * @var bool
     * @ORM\Column(name="lasf_insurance", type="boolean", nullable=true)
     */
    private $lasfInsurance;

    /**
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    private $urgency;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->representatives = new ArrayCollection();
        $this->sports = new ArrayCollection();
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
    public function getTeamName()
    {
        return $this->teamName;
    }

    /**
     * @param string $teamName
     */
    public function setTeamName($teamName)
    {
        $this->teamName = $teamName;
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
    public function getLasfAddress()
    {
        return $this->lasfAddress;
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
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * @param string $mobileNumber
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
    }

    /**
     * @return string
     */
    public function getManagerFullName()
    {
        return $this->managerFullName;
    }

    /**
     * @param string $managerFullName
     */
    public function setManagerFullName($managerFullName)
    {
        $this->managerFullName = $managerFullName;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $createAt
     */
    public function setCreateAt(\DateTime $createAt)
    {
        $this->createdAt = $createAt;
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $expiresAt
     */
    public function setExpiresAt(\DateTime $expiresAt = null)
    {
        $this->expiresAt = $expiresAt;
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
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param array $statusMap
     * @return bool
     */
    public function hasStatus($statusMap)
    {
        return in_array($this->getStatus(), $statusMap);
    }

    /**
     * @return bool
     */
    public function hasRejectedFiles()
    {
        foreach ($this->documents as $document) {
            if ($document->isRejected()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $user
     * @param Licence|null $licence
     * @return bool
     */
    public function isOwner(User $user, Licence $licence = null)
    {
        $declarant = false;
        $owner = false;
        if ($licence) {
            if ($licence->getLicence()) {
                $owner = $this->getLicence()->getUser()->getId() == $user->getId();
            }
            if ($licence->getDeclarant()) {
                $declarant = $this->getDeclarant()->getId() == $user->getId();
            }
        }
        return $this->getUser()->getId() == $user->getId() || $owner || $declarant;
    }

    /**
     * @return bool
     */
    public function isConfirmable()
    {
        return !$this->hasRejectedFiles() && in_array($this->status, [
            self::STATUS_WAITING_CONFIRM,
            self::STATUS_UPLOADED,
            self::STATUS_EXTEND,
        ]);
    }

    /**
     * @return bool
     */
    public function isDriverLicence()
    {
        return in_array($this->getType(), self::$driverTypes);
    }

    /**
     * @return string
     */
    public function getPersonalCode()
    {
        return $this->personalCode;
    }

    /**
     * @param string $personalCode
     */
    public function setPersonalCode($personalCode)
    {
        $this->personalCode = $personalCode;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
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
        if (!$licence->isDeclarantLicence()) {
            throw new \RuntimeException('Provided licence should be a declarant licence.');
        }

        $this->licence = $licence;
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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
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

    public function getTranslationStatus()
    {
        if ($this->isMembershipLicence() && $this->isProduced()) {
            return 'licences.status.produced_membership';
        }

        return 'licences.status.' . $this->getStatus();
    }

    public function isJudgeLicence()
    {
        return in_array($this->getType(), self::$judgeTypes);
    }

    public function isTrackLicence()
    {
        return $this->getType() == self::TYPE_TRACK;
    }

    public function isCompleted()
    {
        return in_array($this->getStatus(), self::$completedStatuses);
    }

    public function isOrganisatorLicence()
    {
        return in_array($this->getType(), self::$organisatorTypes);
    }

    public function isDeclarantLicence()
    {
        return in_array($this->getType(), self::$declarantTypes);
    }

    public function isMembershipLicence()
    {
        return $this->type == self::TYPE_MEMBERSHIP;
    }

    public function isPaid()
    {
        return $this->status == self::STATUS_PAID;
    }

    public function isNotPaid()
    {
        return $this->status == self::STATUS_NOT_PAID;
    }

    public function isExtend()
    {
        return $this->status == self::STATUS_EXTEND;
    }

    public function isCancelled()
    {
        return $this->status == self::STATUS_CANCELLED;
    }

    public function isInvoiceSent()
    {
        return $this->status == self::STATUS_INVOICE;
    }

    public function isProduced()
    {
        return $this->status == self::STATUS_PRODUCED;
    }

    public function isUnconfirmed()
    {
        return $this->getStatus() == self::STATUS_UNCONFIRMED;
    }

    public function isDeclined()
    {
        return $this->getStatus() == self::STATUS_DECLINED;
    }

    /**
     * @return User
     */
    public function getDeclarant()
    {
        return $this->declarant;
    }

    /**
     * @param User $declarant
     */
    public function setDeclarant(User $declarant = null)
    {
        $this->declarant = $declarant;
    }

    /**
     * @return int
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * @param int $serialNumber
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;
    }

    /**
     * @return string
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * @param string $series
     */
    public function setSeries($series)
    {
        $this->series = $series;
    }

    public function getFullSerial()
    {
        return $this->series ? $this->series . '/' . $this->serialNumber : '-';
    }

    public function isActive()
    {
        return $this->isCompleted() && !$this->isExpired();
    }

    public function isExpired()
    {
        return $this->getExpiresAt()->format('U') < (new \DateTime())->format('U');
    }

    public function isExpiringOrExpired()
    {
        $diff = $this->getExpiresAt()->diff(new \DateTime());

        return $this->isExpired() || ($diff->m == 0 && $diff->y == 0);
    }

    /**
     * @return boolean
     */
    public function isExtending()
    {
        return $this->extending;
    }

    /**
     * @param boolean $extending
     */
    public function setExtending($extending)
    {
        $this->extending = $extending;
    }

    public function getCanBeExtended()
    {
        if (!$this->isProduced()) {
            return false;
        }

        if ($this->licence) {
            if ($this->licence->isExpiringOrExpired() || $this->licence->isUnconfirmed()) {
                return false;
            }
        }
        if ($this->licence) {
            return $this->isExpiringOrExpired() && $this->licence->hasStatus(self::$extendableStatuses);
        }

        return $this->isExpiringOrExpired();
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
     * @return Licence
     */
    public function getBasedOnLicence()
    {
        return $this->basedOnLicence;
    }

    /**
     * @return TeamRepresentative[]|ArrayCollection
     */
    public function getRepresentatives()
    {
        return $this->representatives;
    }

    public function addRepresentative(TeamRepresentative $tr)
    {
        $tr->setLicence($this);
        $this->representatives->add($tr);
    }

    public function removeRepresentative(TeamRepresentative $tr)
    {
        $tr->setLicence(null);
        $this->representatives->removeElement($tr);
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
     * Get sports
     *
     * @return Sport[]|ArrayCollection
     */
    public function getSports()
    {
        return $this->sports;
    }

    /**
     * @param ArrayCollection $sports
     */
    public function setSports($sports)
    {
        $this->sports = $sports;
    }

    /**
     * @return bool
     */
    public function isFirstDriver()
    {
        return $this->firstDriver;
    }

    /**
     * @param bool $firstDriver
     */
    public function setFirstDriver($firstDriver)
    {
        $this->firstDriver = $firstDriver;
    }

    /**
     * @return bool
     */
    public function isSecondDriver()
    {
        return $this->secondDriver;
    }

    /**
     * @param bool $secondDriver
     */
    public function setSecondDriver($secondDriver)
    {
        $this->secondDriver = $secondDriver;
    }

    /**
     * @return Language[]|ArrayCollection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param Language $language
     * @return Licence
     */
    public function addLanguage(Language $language)
    {
        if (!$this->getLanguages()->contains($language)) {
            $this->getLanguages()->add($language);
        }

        return $this;
    }

    /**
     * @param $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * @param Language $language
     * @return Licence
     */
    public function removeLanguage(Language $language)
    {
        $this->getLanguages()->removeElement($language);

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentityCode()
    {
        return $this->identityCode;
    }

    /**
     * @param string $identityCode
     */
    public function setIdentityCode($identityCode)
    {
        $this->identityCode = $identityCode;
    }

    /**
     * @return boolean
     */
    public function isLasfInsurance()
    {
        return $this->lasfInsurance;
    }

    /**
     * @param boolean $lasfInsurance
     */
    public function setLasfInsurance($lasfInsurance)
    {
        $this->lasfInsurance = $lasfInsurance;
    }

    /**
     * @return array
     */
    public function getDocumentsTypes()
    {
        $type = $this->extractType($this->getType());
        if ($type) {
            $documents = [];
            if (isset(FileUpload::$licenceDocuments[$type])) {
                $documents = FileUpload::$licenceDocuments[$type];
            }

            $key = array_search(FileUpload::TYPE_PARENT_AGREEMENT, $documents);
            if ($this->getUser()->isMature() && $key !== false) {
                unset($documents[$key]);
            }

            $key = array_search(FileUpload::TYPE_EXAM, $documents);
            if (($this->isType(self::TYPE_DRIVER_B) ||
                $this->isType(self::TYPE_DRIVER_C) ||
                $this->isType(self::TYPE_DRIVER_R)) &&
                $key !== false
            ) {
                unset($documents[$key]);
            }

            $key = array_search(FileUpload::TYPE_RALLY_PARTICIPANT, $documents);
            if (!$this->isType(self::TYPE_DRIVER_D) &&
                !$this->isType(self::TYPE_DRIVER_D2) &&
                !$this->isType(self::TYPE_DRIVER_JRD) &&
                !$this->isType(self::TYPE_DRIVER_R) &&
                $key !== false

            ) {
                unset($documents[$key]);
            }

            return $documents;
        }
    }

    /**
     * @param $type
     * @return null|string
     */
    public function extractType($type)
    {
        $licenceType = explode('.', $type)[0];
        switch ($licenceType) {
            case 'driver_licence':
                return self::DRIVER_TYPE;
            case 'judge_licence':
                return self::JUDGE_TYPE;
            case 'declarant_licence':
                return self::DECLARANT_TYPE;
            case 'organisator_licence':
                return self::ORGANISATOR_TYPE;
            case 'track_licence':
                return self::TRACK_TYPE;
            case 'safety_licence':
                return self::SAFETY_TYPE;
            case 'membership':
                return self::MEMBERSHIP_TYPE;
            default:
                return null;
        }
    }

    /**
     * @param $type
     * @return bool
     */
    private function isType($type)
    {
        return $this->getType() == $type;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getDocumentsByType($type)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('type', $type))
            ->andWhere(Criteria::expr()->orX(
                Criteria::expr()->eq('status', self::STATUS_DOCUMENT_NEW),
                Criteria::expr()->eq('status', self::STATUS_DOCUMENT_APPROVED))
            );

        return $this->getDocuments()->matching($criteria);
    }

    /**
     * @return string
     */
    public function getUrgency()
    {
        return $this->urgency;
    }

    /**
     * @param string $urgency
     */
    public function setUrgency($urgency)
    {
        $this->urgency = $urgency;
    }

    /**
     * @return string
     */
    public function getLicenceNumber()
    {
        return $this->getSeries().'/'.$this->getSerialNumber();
    }
}
