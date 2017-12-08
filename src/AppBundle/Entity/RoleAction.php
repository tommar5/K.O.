<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;

/**
 * RoleAction
 *
 * @ORM\Table(name="role_action")
 * @ORM\Entity
 */
class RoleAction
{
    static public $roleMap = [
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
     * @ORM\Column(length=64, nullable=true)
     */
    private $action;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $roles = 0;

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
     * Get roles number
     *
     * @return int
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set roles number
     *
     * @param array $roles
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set action
     *
     * @param $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get roles array
     *
     * @return array
     */
    public function getRolesArray()
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
     * Check if action is allowed for given roles
     *
     * @param array $roles
     * @return bool
     */
    public function isActionAllowed($roles)
    {
        $allowedRoles = $this->getRolesArray();
        $compareResult = array_intersect($allowedRoles, $roles);

        if (!empty($compareResult)){
            return true;
        }
        return false;
    }
}
