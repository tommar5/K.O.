<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

abstract class UserInfo
{
    const GENDER_MALE = 2;
    const GENDER_FEMALE = 1;

    public static $genderMap = [
        self::GENDER_FEMALE => 'user.label.genders.female',
        self::GENDER_MALE => 'user.label.genders.male',
    ];

    /**
     * @var string
     * @ORM\Column(type="string", length=80, nullable=true)
     * @Assert\Length(max=80)
     * @Assert\Email()
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=36, nullable=true)
     * @Assert\Length(max=36)
     * @Assert\Regex(pattern="/^\+?\d+$/", message="user.profile.incorrect_phone")
     */
    protected $phone;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @Assert\Length(max=1)
     * @ORM\Column(type="smallint", nullable=true, options={"default":2})
     */
    protected $gender;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $secondaryLanguage;

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getSecondaryLanguage()
    {
        return $this->secondaryLanguage;
    }

    /**
     * @param string $secondaryLanguage
     */
    public function setSecondaryLanguage($secondaryLanguage)
    {
        $this->secondaryLanguage = $secondaryLanguage;
    }
}
