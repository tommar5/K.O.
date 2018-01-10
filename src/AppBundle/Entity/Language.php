<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="languages")
 */
class Language
{
    const OTHER_LANGUAGE = "Kita";
    const LT_LANGUAGE = "Lietuvių";
    const RU_LANGUAGE = "Rusų";
    const EN_LANGUAGE = "Anglų";
    const FR_LANGUAGE = "Prancūzų";
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @Assert\Length(max=20)
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $language;

    /**
     * @var User[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="User", mappedBy="languages")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $user
     *
     * @return Language
     */
    public function addUser(User $user)
    {
        $this->getUsers()->add($user);

        return $this;
    }

    /**
     * @param User $user
     *
     * @return Language
     */
    public function removeUser(User $user)
    {
        $this->getUsers()->removeElement($user);

        return $this;
    }
}
