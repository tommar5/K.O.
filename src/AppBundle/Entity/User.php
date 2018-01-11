<?php namespace AppBundle\Entity;

use AppBundle\Mailer\ContactInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\Criteria;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use AppBundle\Validator\Constraints as AppAssert;

/**
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(name="users")
 * @UniqueEntity("email")
 * @Vich\Uploadable
 */
class User extends UserInfo implements UserInterface, \Serializable, ContactInterface, AdvancedUserInterface
{
    static public $roleMap = [
        'ROLE_USER' => 0,              // vartotojas
        'ROLE_RACER' => 1,             // sportininkas
        'ROLE_DECLARANT' => 2,         // pareiskejas
        'ROLE_ORGANISATOR' => 4,       // organizatorius
        'ROLE_JUDGE' => 8,             // teisejas
        'ROLE_ADMIN' => 16,            // administratorius
        'ROLE_DEPARTMENT' => 32,       // departamentas
        'ROLE_ACCOUNTANT' => 64,       // buhaltere
        'ROLE_SPECTATOR' => 128,       // stebetojas
        //add new 256 here
        'ROLE_LASF_COMMITTEE' => 512,  // LASF komitetas
        'ROLE_SVO_COMMITTEE' => 1024,  // SVO komitetas
        'ROLE_COMPETITION_CHIEF' => 2048, // varzybu vadovas
        //add new 4096 here
        'ROLE_CHAIRMAN' => 8192,       // pirmininkas
        'ROLE_PRESIDENT' => 16384,     // prezidentas
        'ROLE_SECRETARY' => 32768,     // sekretore
        'ROLE_JUDGE_COMMITTEE' => 65536,     // teiseju komitetas
        'ROLE_SKK_HEAD' => 131072,     // SKK pirmininkas
    ];

    const ROLE_LASF_COMMITTEE = 'ROLE_LASF_COMMITTEE';
    const ROLE_ORGANISATOR = 'ROLE_ORGANISATOR';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(length=48)
     */
    private $salt;

    /**
     * @ORM\Column(length=64, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(length=64, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(name="token", length=48, nullable=true)
     */
    private $token;

    /**
     * If disabled, user cannot login (user is blocked)
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled = true;

    /**
     * If unconfirmed, user cannot login (user is not yet confirmed by admin)
     *
     * @ORM\Column(type="boolean")
     */
    private $confirmed = true;

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="terms_confirmed")
     */
    private $termsConfirmed = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="change_password")
     */
    private $changePassword = true;

    /**
     * @ORM\Column(type="integer")
     */
    private $roles = 0;

    /**
     * @var string
     * @ORM\Column(name="about_me", type="text", nullable=true)
     * @Assert\Length(max=16000)
     */
    private $aboutMe = '';

    /**
     * @var string
     * @ORM\Column(name="notes", type="text", nullable=true)
     * @Assert\Length(max=16000)
     */
    private $notes = '';

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", name="updated_at")
     */
    private $updatedAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="last_login_at", nullable=true)
     */
    private $lastLoginAt;

    /**
     * @ORM\Column(length=64, nullable=true)
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(length=255, nullable=true)
     */
    private $prefLicence;

    /**
     * @var bool
     * @ORM\Column(name="receive_notifications", type="boolean")
     */
    private $receiveNotifications = true;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @Assert\NotBlank(message="Password cannot be empty", groups={"confirm"})
     * @Assert\Length(min=8, max=4096)
     */
    private $plainPassword;

    /**
     * @var ArrayCollection|CompetitionJudge[]
     * @ORM\OneToMany(targetEntity="CompetitionJudge", mappedBy="user")
     */
    private $competitions;

    /**
     * @var Competition[]
     * @ORM\OneToMany(targetEntity="Competition", mappedBy="mainJudge")
     */
    private $leadedCompetitions;

    /**
     * @var Competition[]
     * @ORM\OneToMany(targetEntity="Competition", mappedBy="user")
     */
    private $organizedCompetitions;

    /**
     * @var CompetitionParticipant[]
     * @ORM\OneToMany(targetEntity="CompetitionParticipant", mappedBy="user")
     */
    private $races;

    /**
     * @var FileUpload[]
     *
     * @OneToMany(targetEntity="FileUpload", mappedBy="user")
     */
    private $documents;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="children")
     */
    private $parent;

    /**
     * @var ArrayCollection|User[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="parent")
     */
    private $children;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(length=255, nullable=true)
     */
    private $address;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(length=255, nullable=true)
     */
    private $identityCode;

    /**
     * @var string
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * @Assert\Image(
     *  mimeTypes = {
     *      "image/png",
     *      "image/jpeg",
     *      "image/jpg",
     *      "image/gif",
     *  },
     *  maxSize = "2048k",
     *  mimeTypesMessage = "user.profile.bad_image_type"
     * )
     * @Vich\UploadableField(mapping="image", fileNameProperty="imageName")
     * @var File
     */
    private $imageFile;

    /**
     * @var bool
     * @ORM\Column(name="legal", type="boolean")
     */
    private $legal = false;

    /**
     * @var bool
     * @ORM\Column(name="associated", type="boolean", nullable=true)
     */
    private $associated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     * @Assert\Date()
     */
    private $birthday;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(length=255, nullable=true)
     */
    private $bank;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(length=255, nullable=true)
     */
    private $bankAccount;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $memberName;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $memberCode;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vatCode;

    /**
     * @var Application[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Application", mappedBy="skkHead")
     */
    private $skkApplications;

    /**
     * @var Application[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Application", mappedBy="observer")
     */
    private $observerApplications;

    /**
     * @var Application[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Application", mappedBy="svoDelegate")
     */
    private $svoDelegateApplications;

    /**
     * Team members if user is declarant
     *
     * @var User
     * @ORM\ManyToMany(targetEntity="User", inversedBy="declarants")
     */
    private $members;

    /**
     * @var User
     * @ORM\ManyToMany(targetEntity="User", mappedBy="members")
     */
    private $declarants;

    /**
     * @var Application[]
     *
     * @OneToMany(targetEntity="Application", mappedBy="user")
     */
    private $applications;

    /**
     * @var CompetitionChief[]
     *
     * @OneToMany(targetEntity="CompetitionChief", mappedBy="user")
     */
    private $competitionChiefs;

    /**
     * @var Steward[]
     *
     * @OneToMany(targetEntity="Steward", mappedBy="user")
     */
    private $stewards;

    /**
     * @var Comment[]
     *
     * @OneToMany(targetEntity="Comment", mappedBy="user")
     */
    private $comments;

    /**
     * @var MusicStyle[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MusicStyle", cascade={"persist"}, inversedBy="committes")
     * @ORM\JoinTable(name="committe_sports",
     *      joinColumns={@ORM\JoinColumn(name="committe_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sport_id", referencedColumnName="id")}
     * )
     */
    private $musicStyles;

    /**
     * @var Language[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Language", cascade={"persist"}, inversedBy="users")
     * @ORM\JoinTable(name="user_languages",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="language_id", referencedColumnName="id")}
     * )
     */
    private $languages;

    /**
     * @var FavouriteSong[]|ArrayCollection
     *
     * Many Users have Many Songs.
     * @ORM\ManyToMany(targetEntity="Music")
     * @ORM\JoinTable(name="favorite_songs",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="song_id", referencedColumnName="id")}
     *      )
     */
    private $favoriteSongs;

    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->documents = new ArrayCollection();
        $this->competitions = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->races = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->declarants = new ArrayCollection();
        $this->competitionChiefs = new ArrayCollection();
        $this->stewards = new ArrayCollection();
        $this->skkApplications = new ArrayCollection();
        $this->observerApplications = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->musicStyles = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->svoDelegateApplications = new ArrayCollection();
        $this->favoriteSongs = new ArrayCollection();
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

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function getRoles()
    {
        $roles = [];
        foreach (self::$roleMap as $role => $flag) {
            if ($flag === ($this->roles & $flag)) {
                $roles[] = $role;
            }
        }
        return $roles;
    }

    /**
     * @return int
     */
    public function getRole()
    {
        return $this->roles;
    }

    public function hasRole($role)
    {
        return in_array($role, $this->getRoles(), true);
    }

    public function removeRole($role)
    {
        if (!$this->hasRole($role)) {
            return $this;
        }

        $role = strtoupper($role);
        if (array_key_exists($role, self::$roleMap)) {
            $this->roles ^= self::$roleMap[$role];
        }
        return $this;
    }

    public function setRole($role)
    {
        $this->roles = $role;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = 0;
        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function addRole($role)
    {
        if ($this->hasRole($role)) {
            return $this;
        }

        $role = strtoupper($role);
        if (!array_key_exists($role, self::$roleMap)) {
            return $this;
        }
        $this->roles |= self::$roleMap[$role];
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
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

    public function addDocument(FileUpload $document)
    {
        $document->setUser($this);
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
        }
    }

    public function removeDocument(FileUpload $document)
    {
        $this->documents->removeElement($document);
    }

    public function regenerateToken()
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes(32, $strong);

            if (false !== $bytes && true === $strong) {
                $num = $bytes;
            } else {
                $num = hash('sha256', uniqid(mt_rand(), true), true);
            }
        } else {
            $num = hash('sha256', uniqid(mt_rand(), true), true);
        }

        $this->token = rtrim(strtr(base64_encode($num), '+/', '-_'), '=');
        return $this;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function __toString()
    {
        return $this->firstname ? trim($this->firstname . ' ' . $this->lastname) : $this->getEmail();
    }

    public function serialize()
    {
        return serialize([
            $this->getEmail(),
            $this->id,
            $this->roles,
            $this->enabled,
        ]);
    }

    public function unserialize($serialized)
    {
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge(unserialize($serialized), array_fill(0, 4, null));

        list($email, $this->id, $this->roles, $this->enabled) = $data;
        $this->setEmail($email);
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @param \AppBundle\Entity\CompetitionJudge $competition
     *
     * @return User
     */
    public function addCompetition(CompetitionJudge $competition)
    {
        $this->competitions->add($competition);

        return $this;
    }

    /**
     * @param \AppBundle\Entity\CompetitionJudge $competition
     */
    public function removeCompetition(CompetitionJudge $competition)
    {
        $this->competitions->removeElement($competition);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompetitions()
    {
        return $this->competitions;
    }

    /**
     * @return FileUpload[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @param FileUpload[] $documents
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;
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
     * @return mixed
     */
    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
    }

    /**
     * @param \DateTime $lastLoginAt
     */
    public function setLastLoginAt(\DateTime $lastLoginAt = null)
    {
        $this->lastLoginAt = $lastLoginAt;
    }

    /**
     * @return string
     */
    public function getAboutMe()
    {
        return $this->aboutMe;
    }

    /**
     * @param string $aboutMe
     */
    public function setAboutMe($aboutMe)
    {
        $this->aboutMe = $aboutMe;
    }

    /**
     * @return User
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param User $parent
     */
    public function setParent(User $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return User[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param User $children
     */
    public function addChildren($children)
    {
        $children->setParent($this);
        $this->children->add($children);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isParent(User $user)
    {
        return $user && $this->getParent() && $this->getParent()->getId() == $user->getId();
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return $this->confirmed;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return Application[]|ArrayCollection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @param Licence $applications
     */
    public function setApplications($applications)
    {
        $this->applications = $applications;
    }

    /**
     * @return CompetitionParticipant[]|ArrayCollection
     */
    public function getRaces()
    {
        return $this->races;
    }

    /**
     * @param CompetitionParticipant $race
     */
    public function addRace(CompetitionParticipant $race)
    {
        $this->races->add($race);
    }

    /**
     * @param CompetitionParticipant $race
     */
    public function removeRace(CompetitionParticipant $race)
    {
        $this->races->removeElement($race);
    }

    public function participatesInCompetition(Competition $comp)
    {
        foreach ($this->races as $race) {
            if ($race->getCompetition()->getId() == $comp->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
     * @return Competition[]
     */
    public function getLeadingCompetitions()
    {
        return $this->leadedCompetitions;
    }

    /**
     * @param Competition[] $leadedCompetitions
     */
    public function setLeadingCompetitions($leadedCompetitions)
    {
        $this->leadedCompetitions = $leadedCompetitions;
    }

    /**
     * @return Competition[]
     */
    public function getOrganizedCompetitions()
    {
        return $this->organizedCompetitions;
    }

    /**
     * @param Competition[] $organizedCompetitions
     */
    public function setOrganizedCompetitions($organizedCompetitions)
    {
        $this->organizedCompetitions = $organizedCompetitions;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getMemberName()
    {
        return $this->memberName;
    }

    /**
     * @param string $memberName
     */
    public function setMemberName($memberName)
    {
        $this->memberName = $memberName;
    }

    /**
     * @return mixed
     */
    public function getMemberCode()
    {
        return $this->memberCode;
    }

    /**
     * @param mixed $memberCode
     */
    public function setMemberCode($memberCode)
    {
        $this->memberCode = $memberCode;
    }

    /**
     * @return mixed
     */
    public function getVatCode()
    {
        return $this->vatCode;
    }

    /**
     * @param mixed $vatCode
     */
    public function setVatCode($vatCode)
    {
        $this->vatCode = $vatCode;
    }

    public static function toTranslation($role)
    {
        return 'user.roles.' . strtolower(substr($role, strpos($role, '_') + 1));
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param User $member
     */
    public function addMember(User $member)
    {
        if ($this->members->contains($member)) {
            return;
        }

        $this->members->add($member);
    }

    public function removeMember(User $user)
    {
        $this->members->removeElement($user);
    }

    /**
     * @return boolean
     */
    public function isLegal()
    {
        return $this->legal;
    }

    /**
     * @param boolean $legal
     */
    public function setLegal($legal)
    {
        $this->legal = $legal;
    }

    /**
     * @return boolean
     */
    public function isAssociated()
    {
        return $this->associated;
    }

    /**
     * @param boolean $associated
     */
    public function setAssociated($associated = null)
    {
        $this->associated = $associated;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * Check if user has legal age (18y+)
     */
    public function isMature()
    {
        return $this->birthday && (new \DateTime())->diff($this->birthday)->y >= 18;
    }

    public function hasActiveMembership()
    {
        return true;
    }

    public function hasAskedForMembership()
    {
        return true;
    }

    public function hasActiveDLicence()
    {
        return true;
    }

    public function hadDriverLicence()
    {
        return true;
    }

    /**
     * @return boolean
     */
    public function receiveNotifications()
    {
        return $this->receiveNotifications;
    }

    /**
     * @param boolean $receiveNotifications
     */
    public function setReceiveNotifications($receiveNotifications)
    {
        $this->receiveNotifications = $receiveNotifications;
    }

    /**
     * @return bool
     */
    public function getChangePassword()
    {
        return $this->changePassword;
    }

    /**
     * @param bool $changePassword
     */
    public function setChangePassword($changePassword)
    {
        $this->changePassword = $changePassword;
    }

    /**
     * @return bool
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @param bool $confirmed
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @param Application $skkApplication
     * @return User
     */
    public function addSkkApplication(Application $skkApplication)
    {
        $this->skkApplications->add($skkApplication);

        return $this;
    }

    /**
     * @param Application $skkApplication
     */
    public function removeSkkApplication(Application $skkApplication)
    {
        $this->skkApplications->removeElement($skkApplication);
    }

    /**
     * @return Application[]
     */
    public function getSkkApplications()
    {
        return $this->skkApplications;
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getDeclarants()
    {
        return $this->declarants;
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
        $this->competitionChiefs->removeElement($steward);
    }

    /**
     * @return Comment[]|ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @param MusicStyle $musicStyle
     */
    public function addMusicStyle(MusicStyle $musicStyle)
    {
        $this->musicStyles->add($musicStyle);
    }

    public function removeMusicStyles(MusicStyle $musicStyle)
    {
        $this->musicStyles->removeElement($musicStyle);
    }

    /**
     * Get musicStyles
     *
     * @return MusicStyle[]|ArrayCollection
     */
    public function getMusicStyles()
    {
        return $this->musicStyles;
    }

    /**
     * @param ArrayCollection $musicStyles
     */
    public function setMusicStyles($musicStyles)
    {
        $this->musicStyles = $musicStyles;
    }

    /**
     * @param MusicStyle $musicStyle
     * @return bool
     */
    public function hasMusicStyle(MusicStyle $musicStyle)
    {
        return $this->getMusicStyles()->contains($musicStyle);
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
     * @return User
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
     * @return User
     */
    public function removeLanguage(Language $language)
    {
        $this->getLanguages()->removeElement($language);

        return $this;
    }

    /**
     * @return bool
     */
    public function canSeeAllApplications()
    {
        return $this->hasRole('ROLE_ADMIN') ||
               $this->hasRole('ROLE_ACCOUNTANT') ||
               $this->hasRole('ROLE_SKK_HEAD') ||
               $this->hasRole('ROLE_SPECTATOR') ||
               $this->hasRole('ROLE_SVO_COMMITTEE') ||
               $this->hasRole('ROLE_SECRETARY') ||
               $this->hasRole('ROLE_LASF_COMMITTEE') ||
               $this->hasRole('ROLE_COMPETITION_CHIEF') ||
               $this->hasRole('ROLE_JUDGE_COMMITTEE');
    }

    /**
     * @return FavouriteSong[]|ArrayCollection
     */
    public function getFavoriteSongs()
    {
        return $this->favoriteSongs;
    }

    /**
     * @param FavouriteSong[]|ArrayCollection $favoriteSongs
     */
    public function setFavoriteSongs($favoriteSongs)
    {
        $this->favoriteSongs = $favoriteSongs;
    }
}
