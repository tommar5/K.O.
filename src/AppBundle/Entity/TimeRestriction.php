<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DateRestriction
 * @ORM\Table()
 *
 * @ORM\Entity
 */
class TimeRestriction
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/^\+?\d+$/", message="time_restriction.invalid_number")
     * @Assert\Length(max=255)
     */
    private $payTaxesTerm;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/^\+?\d+$/", message="time_restriction.invalid_number")
     * @Assert\Length(max=255)
     */
    private $additionalRulesTerm;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/^\+?\d+$/", message="time_restriction.invalid_number")
     * @Assert\Length(max=255)
     */
    private $additionalRulesConfirmationTerm;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/^\+?\d+$/", message="time_restriction.invalid_number")
     * @Assert\Length(max=255)
     */
    private $safetyPlanTerm;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/^\+?\d+$/", message="time_restriction.invalid_number")
     * @Assert\Length(max=255)
     */
    private $finalRulesTerm;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/^\+?\d+$/", message="time_restriction.invalid_number")
     * @Assert\Length(max=255)
     */
    private $contactAdministrationTerm;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/^\+?\d+$/", message="time_restriction.invalid_number")
     * @Assert\Length(max=255)
     */
    private $trackActTerm;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPayTaxesTerm = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAdditionalRulesTerm = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAdditionalRulesConfirmationTerm = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isSafetyPlanTerm = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isFinalRulesTerm = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isContactAdministrationTerm = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isTrackActTerm = false;


    /**
     * Set payTaxesTerm
     *
     * @param string $payTaxesTerm
     *
     * @return TimeRestriction
     */
    public function setPayTaxesTerm($payTaxesTerm)
    {
        $this->payTaxesTerm = $payTaxesTerm;

        return $this;
    }

    /**
     * Get payTaxesTerm
     *
     * @return string
     */
    public function getPayTaxesTerm()
    {
        return $this->payTaxesTerm;
    }

    /**
     * Set additionalRulesTerm
     *
     * @param string $additionalRulesTerm
     *
     * @return TimeRestriction
     */
    public function setAdditionalRulesTerm($additionalRulesTerm)
    {
        $this->additionalRulesTerm = $additionalRulesTerm;

        return $this;
    }

    /**
     * Get additionalRulesTerm
     *
     * @return string
     */
    public function getAdditionalRulesTerm()
    {
        return $this->additionalRulesTerm;
    }

    /**
     * Set additionalRulesConfirmationTerm
     *
     * @param string $additionalRulesConfirmationTerm
     *
     * @return TimeRestriction
     */
    public function setAdditionalRulesConfirmationTerm($additionalRulesConfirmationTerm)
    {
        $this->additionalRulesConfirmationTerm = $additionalRulesConfirmationTerm;

        return $this;
    }

    /**
     * Get additionalRulesConfirmationTerm
     *
     * @return string
     */
    public function getAdditionalRulesConfirmationTerm()
    {
        return $this->additionalRulesConfirmationTerm;
    }

    /**
     * Set safetyPlanTerm
     *
     * @param string $safetyPlanTerm
     *
     * @return TimeRestriction
     */
    public function setSafetyPlanTerm($safetyPlanTerm)
    {
        $this->safetyPlanTerm = $safetyPlanTerm;

        return $this;
    }

    /**
     * Get safetyPlanTerm
     *
     * @return string
     */
    public function getSafetyPlanTerm()
    {
        return $this->safetyPlanTerm;
    }

    /**
     * Set finalRulesTerm
     *
     * @param string $finalRulesTerm
     *
     * @return TimeRestriction
     */
    public function setFinalRulesTerm($finalRulesTerm)
    {
        $this->finalRulesTerm = $finalRulesTerm;

        return $this;
    }

    /**
     * Get finalRulesTerm
     *
     * @return string
     */
    public function getFinalRulesTerm()
    {
        return $this->finalRulesTerm;
    }

    /**
     * Set contactAdministrationTerm
     *
     * @param string $contactAdministrationTerm
     *
     * @return TimeRestriction
     */
    public function setContactAdministrationTerm($contactAdministrationTerm)
    {
        $this->contactAdministrationTerm = $contactAdministrationTerm;

        return $this;
    }

    /**
     * Get contactAdministrationTerm
     *
     * @return string
     */
    public function getContactAdministrationTerm()
    {
        return $this->contactAdministrationTerm;
    }

    /**
     * Set trackActTerm
     *
     * @param string $trackActTerm
     *
     * @return TimeRestriction
     */
    public function setTrackActTerm($trackActTerm)
    {
        $this->trackActTerm = $trackActTerm;

        return $this;
    }

    /**
     * Get trackActTerm
     *
     * @return string
     */
    public function getTrackActTerm()
    {
        return $this->trackActTerm;
    }

    /**
     * Set isPayTaxesTerm
     *
     * @param boolean $isPayTaxesTerm
     *
     * @return TimeRestriction
     */
    public function setIsPayTaxesTerm($isPayTaxesTerm)
    {
        $this->isPayTaxesTerm = $isPayTaxesTerm;

        return $this;
    }

    /**
     * Get isPayTaxesTerm
     *
     * @return boolean
     */
    public function getIsPayTaxesTerm()
    {
        return $this->isPayTaxesTerm;
    }

    /**
     * Set isAdditionalRulesTerm
     *
     * @param boolean $isAdditionalRulesTerm
     *
     * @return TimeRestriction
     */
    public function setIsAdditionalRulesTerm($isAdditionalRulesTerm)
    {
        $this->isAdditionalRulesTerm = $isAdditionalRulesTerm;

        return $this;
    }

    /**
     * Get isAdditionalRulesTerm
     *
     * @return boolean
     */
    public function getIsAdditionalRulesTerm()
    {
        return $this->isAdditionalRulesTerm;
    }

    /**
     * Set isAdditionalRulesConfirmationTerm
     *
     * @param boolean $isAdditionalRulesConfirmationTerm
     *
     * @return TimeRestriction
     */
    public function setIsAdditionalRulesConfirmationTerm($isAdditionalRulesConfirmationTerm)
    {
        $this->isAdditionalRulesConfirmationTerm = $isAdditionalRulesConfirmationTerm;

        return $this;
    }

    /**
     * Get isAdditionalRulesConfirmationTerm
     *
     * @return boolean
     */
    public function getIsAdditionalRulesConfirmationTerm()
    {
        return $this->isAdditionalRulesConfirmationTerm;
    }

    /**
     * Set isSafetyPlanTerm
     *
     * @param boolean $isSafetyPlanTerm
     *
     * @return TimeRestriction
     */
    public function setIsSafetyPlanTerm($isSafetyPlanTerm)
    {
        $this->isSafetyPlanTerm = $isSafetyPlanTerm;

        return $this;
    }

    /**
     * Get isSafetyPlanTerm
     *
     * @return boolean
     */
    public function getIsSafetyPlanTerm()
    {
        return $this->isSafetyPlanTerm;
    }

    /**
     * Set isFinalRulesTerm
     *
     * @param boolean $isFinalRulesTerm
     *
     * @return TimeRestriction
     */
    public function setIsFinalRulesTerm($isFinalRulesTerm)
    {
        $this->isFinalRulesTerm = $isFinalRulesTerm;

        return $this;
    }

    /**
     * Get isFinalRulesTerm
     *
     * @return boolean
     */
    public function getIsFinalRulesTerm()
    {
        return $this->isFinalRulesTerm;
    }

    /**
     * Set isContactAdministrationTerm
     *
     * @param boolean $isContactAdministrationTerm
     *
     * @return TimeRestriction
     */
    public function setIsContactAdministrationTerm($isContactAdministrationTerm)
    {
        $this->isContactAdministrationTerm = $isContactAdministrationTerm;

        return $this;
    }

    /**
     * Get isContactAdministrationTerm
     *
     * @return boolean
     */
    public function getIsContactAdministrationTerm()
    {
        return $this->isContactAdministrationTerm;
    }

    /**
     * Set isTrackActTerm
     *
     * @param boolean $isTrackActTerm
     *
     * @return TimeRestriction
     */
    public function setIsTrackActTerm($isTrackActTerm)
    {
        $this->isTrackActTerm = $isTrackActTerm;

        return $this;
    }

    /**
     * Get isTrackActTerm
     *
     * @return boolean
     */
    public function getIsTrackActTerm()
    {
        return $this->isTrackActTerm;
    }
}
