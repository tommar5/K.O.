<?php namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @ORM\Entity(repositoryClass="FileUploadRepository")
 * @ORM\Table()
 */
class FileUpload
{
    const STATUS_NEW      = 'new';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REVOKED  = 'revoked';

    const STATUS_ADDED_ADDITIONAL_COMPETITION_RULES                  = 'added_additional_competition_rules';
    const STATUS_COMMENTED_ADDITIONAL_COMPETITION_RULES              = 'commented_additional_competition_rules';
    const STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES_BY_LASF      = 'confirmed_additional_competition_rules_by_last';
    const STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES_BY_SECRETARY = 'confirmed_additional_competition_rules_by_secretary';
    const STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES              = 'confirmed_additional_competition_rules';
    const STATUS_ADDED_SAFETY_PLAN                                   = 'added_safety_plan';
    const STATUS_COMMENTED_SAFETY_PLAN                               = 'commented_safety_plan';
    const STATUS_CONFIRMED_SAFETY_PLAN                               = 'confirmed_safety_plan';
    const STATUS_ADDED_TRACK_ACCEPTANCE                              = 'added_track_acceptance';
    const STATUS_CONFIRMED_TRACK_ACCEPTANCE                          = 'confirmed_track_acceptance';
    const STATUS_ADDED_TRACK_LICENCE                                 = 'added_track_licence';
    const STATUS_CONFIRMED_TRACK_LICENCE                             = 'confirmed_track_licence';
    const STATUS_ADDED_COMPETITION_INSURANCE                         = 'added_competition_insurance';
    const STATUS_CONFIRMED_COMPETITION_INSURANCE                     = 'confirmed_competition_insurance';
    const STATUS_ADDED_OTHER_DOCUMENTS                               = 'added_other_documents';
    const STATUS_ADDED_ORGANISATOR_LICENCE                           = 'new';
    const STATUS_CONFIRMED_ORGANISATOR_LICENCE                       = 'confirmed_organisator_licence';

    const TYPE_DRIVERS_LICENCE   = 'driver_licence';
    const TYPE_MED_CERT          = 'medical_certificate';
    const TYPE_PHOTO             = 'photo';
    const TYPE_EXAM              = 'exam';
    const TYPE_SCHOOL_CERT       = 'school_certificate';
    const TYPE_INSURANCE         = 'insurance';
    const TYPE_PARENT_AGREEMENT  = 'parent_agreement';
    const TYPE_PREVIOUS_LICENCE  = 'previous_licence';
    const TYPE_SAFETY_PLAN       = 'safety_plan';
    const TYPE_ACCEPTANCE_ACT    = 'acceptance_act';
    const TYPE_OTHER             = 'other';
    const TYPE_COMP_RESULT       = 'competition_result';
    const TYPE_JUDGE_CERT        = 'judge_certificate';
    const TYPE_RALLY_PARTICIPANT = 'rally_participant';

    const TYPE_RECOMMENDATION = 'membership_recommendation';
    const TYPE_REGISTRY       = 'membership_registry';
    const TYPE_ACTIVITY_DESC  = 'activity_desc';
    const TYPE_STATUTE_COPY   = 'statute_copy';

    const TYPE_APPLICATION_COPY                  = 'application_copy';
    const TYPE_SIGNED_APPLICATION_BY_LASF        = 'signed_application_by_lasf';
    const TYPE_SIGNED_APPLICATION_BY_ORGANISATOR = 'signed_application_by_organisator';
    const TYPE_INVOICE                           = 'invoice';
    const TYPE_COMPETITION_INSURANCE             = 'competition_insurance';
    const TYPE_OTHER_DOCUMENTS                   = 'other_documents';
    const TYPE_ADDITIONAL_RULES                  = 'additional_rules';
    const TYPE_TRACK_ACCEPTANCE                  = 'track_acceptance';
    const TYPE_TRACK_LICENCE                     = 'track_licence';
    const TYPE_ORGANISATOR_LICENCE               = 'organisator_licence';
    const TYPE_COMPETITION_RESULTS               = 'competition_results';
    const TYPE_COMPETITION_REPORT                = 'report';
    const TYPE_COMPETITION_BULLETIN              = 'bulletin';
    const TYPE_COMPETITION_SKK_REPORT            = 'skk_report';

    const TYPE_FILE_MIME                         = ['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',];
    const TYPE_IMAGE_MIME                        = ['image/*',];

    const DECLARANT_TYPE = 'declarant';
    const DRIVER_TYPE = 'driver';
    const JUDGE_TYPE = 'judge';
    const TRACK_TYPE = 'track';
    const MEMBERSHIP_TYPE = 'membership';
    const SAFETY_TYPE = 'safety';
    const ORGANISATOR_TYPE = 'organisator';

    public static $documentsWithoutExpiration = [
        FileUpload::TYPE_PHOTO,
        FileUpload::TYPE_PARENT_AGREEMENT,
        FileUpload::TYPE_PREVIOUS_LICENCE,
        FileUpload::TYPE_ACCEPTANCE_ACT,
        FileUpload::TYPE_OTHER,
        FileUpload::TYPE_SAFETY_PLAN,
        FileUpload::TYPE_COMP_RESULT,
        FileUpload::TYPE_JUDGE_CERT,
        FileUpload::TYPE_RECOMMENDATION,
        FileUpload::TYPE_REGISTRY,
        FileUpload::TYPE_ACTIVITY_DESC,
        FileUpload::TYPE_STATUTE_COPY,
        FileUpload::TYPE_RALLY_PARTICIPANT,
    ];

    public static $requiredDate = [
        FileUpload::TYPE_DRIVERS_LICENCE,
        FileUpload::TYPE_MED_CERT,
        FileUpload::TYPE_INSURANCE,
    ];

    public static $usableOldFiles = [
        FileUpload::TYPE_DRIVERS_LICENCE,
        FileUpload::TYPE_MED_CERT,
        FileUpload::TYPE_PHOTO,
        FileUpload::TYPE_EXAM,
        FileUpload::TYPE_SCHOOL_CERT,
        FileUpload::TYPE_INSURANCE,
        FileUpload::TYPE_PARENT_AGREEMENT,
        FileUpload::TYPE_SAFETY_PLAN,
    ];

    public static $validThisYear = [
        FileUpload::TYPE_INSURANCE,
    ];

    public static $licenceDocuments = [
        self::DRIVER_TYPE => [
            FileUpload::TYPE_MED_CERT,
            FileUpload::TYPE_EXAM,
            FileUpload::TYPE_INSURANCE,
            FileUpload::TYPE_DRIVERS_LICENCE,
            FileUpload::TYPE_PHOTO,
            FileUpload::TYPE_PARENT_AGREEMENT,
            FileUpload::TYPE_RALLY_PARTICIPANT,
        ],
        self::DECLARANT_TYPE => [
            FileUpload::TYPE_RALLY_PARTICIPANT,
        ],
        self::JUDGE_TYPE => [
            FileUpload::TYPE_PHOTO,
            FileUpload::TYPE_PARENT_AGREEMENT,
            FileUpload::TYPE_PREVIOUS_LICENCE,
            FileUpload::TYPE_JUDGE_CERT,
            FileUpload::TYPE_INSURANCE,
        ],
        self::TRACK_TYPE => [
            FileUpload::TYPE_SAFETY_PLAN,
            FileUpload::TYPE_ACCEPTANCE_ACT,
            FileUpload::TYPE_OTHER,
        ],
        self::MEMBERSHIP_TYPE => [
            FileUpload::TYPE_RECOMMENDATION,
            FileUpload::TYPE_REGISTRY,
            FileUpload::TYPE_ACTIVITY_DESC,
            FileUpload::TYPE_STATUTE_COPY,
        ],
        self::SAFETY_TYPE => [],
        self::ORGANISATOR_TYPE => [],
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
     * @ORM\Column(name="type", type="string", length=255)
     * @Assert\NotBlank(groups={"Default", "documents"})
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=255)
     * @Assert\NotBlank(groups={"Default", "documents"})
     */
    private $status = self::STATUS_NEW;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="documents")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="file", type="string")
     */
    private $fileName;

    /**
     * @var File
     */
    private $file;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     * @ORM\Column(name="reason", type="text", nullable=true)
     * @Assert\Length(max=16000)
     */
    private $reason;

    /**
     * @var string
     * @ORM\Column(name="comment", type="text", nullable=true)
     * @Assert\Length(max=16000)
     */
    private $comment;

    /**
     * @var string
     * @ORM\Column(name="number", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $number;

    /**
     * @var \DateTime
     * @ORM\Column(name="valid_until", nullable=true, type="date")
     */
    private $validUntil;

    /**
     * @var Application[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="Application")
     * @ORM\JoinTable(name="application_documents",
     *      joinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id", unique=true)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="application_id", referencedColumnName="id")}
     *     )
     */
    private $applications;

    /**
     * @var Approval[]
     * @OneToMany(targetEntity="Approval", mappedBy="document", orphanRemoval=true)
     */
    private $approvals;

    /**
     * @param string|null $type
     */
    public function __construct($type = null)
    {
        $this->type = $type;
        $this->approvals = new ArrayCollection();
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return FileUpload
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return FileUpload
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
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
     * Checks if status of file is set to new
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->getStatus() == self::STATUS_NEW;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber($number = null)
    {
        $this->number = $number;
    }

    /**
     * @return \DateTime
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    /**
     * @param \DateTime $validUntil
     */
    public function setValidUntil(\DateTime $validUntil = null)
    {
        $this->validUntil = $validUntil;
    }

    /**
     * Checks if reason for status change has been set
     *
     * @return bool
     */
    public function hasReason()
    {
        return !empty($this->getReason());
    }

    /**
     * @return bool
     */
    public function isRejected()
    {
        return $this->getStatus() == self::STATUS_REJECTED;
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->getStatus() == self::STATUS_APPROVED;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isOwner(User $user)
    {
        return $this->getUser()->getId() == $user->getId();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'file_uploads.type.' . $this->getType();
    }

    /**
     * @param ArrayCollection $applications
     */
    public function setApplications(ArrayCollection $applications)
    {
        $this->applications = $applications;
    }

    /**
     * @return Application[]|ArrayCollection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @param $type
     * @return bool
     */
    public function isType($type)
    {
        return $this->getType() == $type;
    }

    /**
     * @param null $type
     * @return bool
     */
    public function canUseOldFile($type = null)
    {
        $type = $type ? : $this->getType();
        return in_array($type, self::$usableOldFiles);
    }

    /**
     * @param null $type
     * @return bool
     */
    public function isDateRequired($type = null)
    {
        $type = $type ? : $this->getType();
        return in_array($type, self::$requiredDate);
    }

    /**
     * @param null $type
     * @return bool
     */
    public function haveDateField($type = null)
    {
        $type = $type ? : $this->getType();
        return !in_array($type, self::$documentsWithoutExpiration);
    }

    /**
     * @param null $type
     * @return bool
     */
    public function isValidThisYear($type = null)
    {
        $type = $type ? : $this->getType();
        return in_array($type, self::$validThisYear);
    }

    /**
     * @param array $statusMap
     * @return bool
     */
    public function hasStatus(array $statusMap)
    {
        return in_array($this->getStatus(), $statusMap);
    }

    /**
     * @param Approval $approval
     */
    public function addApproval(Approval $approval)
    {
        if (!$this->approvals->contains($approval)) {
            $this->approvals->add($approval);
        }
    }

    /**
     * @param Approval $approval
     */
    public function removeApproval(Approval $approval)
    {
        $this->approvals->removeElement($approval);
    }

    /**
     * @return Approval[]|ArrayCollection
     */
    public function getApprovals()
    {
        return $this->approvals;
    }

    /**
     * @param ArrayCollection $approvals
     */
    public function setApprovals(ArrayCollection $approvals)
    {
        $this->approvals = $approvals;
    }
}

