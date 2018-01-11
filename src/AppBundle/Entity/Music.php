<?php namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="MusicRepository")
 * @ORM\Table(name="music")
 * @UniqueEntity("name")
 */
class Music
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
     * @var string
     *
     * @ORM\Column(name="name", length=255, unique=true)
     * @Assert\NotBlank(message="music.name")
     * @Assert\Length(max=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="artist", length=255)
     * @Assert\Length(max=255)
     */
    private $artist;

    /**
     * @var string
     *
     * @ORM\Column(name="album", length=255)
     * @Assert\Length(max=255)
     */
    private $album;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="time", length=255)
     * @Assert\Length(max=255)
     */
    private $time;

    /**
     * @var string
     * @ORM\Column(name="music_file", type="string")
     */
    private $musicFileName;

    /**
     * @var File
     */
    private $musicFile;

    /**
     * Many Songs have Many Users.
     * @ORM\ManyToMany(targetEntity="User", mappedBy="favoriteSongs")
     */
    private $users;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return File
     */
    public function getMusicFile()
    {
        return $this->musicFile;
    }

    /**
     * @param $file
     */
    public function setMusicFile($file)
    {
        $this->musicFile = $file;
    }

    /**
     * @return string
     */
    public function getMusicFileName()
    {
        return $this->musicFileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->musicFileName = $fileName;
    }

    /**
     * @param \DateTime $createdAt
     * @return Music
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

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
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * @param string $artist
     */
    public function setArtist($artist)
    {
        $this->artist = $artist;
    }

    /**
     * @return string
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * @param string $album
     */
    public function setAlbum($album)
    {
        $this->album = $album;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param string $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return Application[]|ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param Application[]|ArrayCollection $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }
}
