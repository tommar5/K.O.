<?php namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="competition_jugde")
 */
class CompetitionJudge
{
    const ROLE_SPORTO_KOMISARAS = 'koncerto_komisaras';
    const ROLE_VARZYBU_VADOVAS = 'koncerto_vadovas';
    const ROLE_VARZYBU_SEKTORIUS = 'koncerto_sektorius';
    const ROLE_LAIKININKAS = 'laikininkas';
    const ROLE_TECHNINIS_KOMISARAS = 'techninis_komisaras';
    const ROLE_TECHNINIS_TEISEJAS = 'techninis_vadovas';
    const ROLE_SAUGUMO_VIRSININKAS = 'saugumo_virsininkas';
    const ROLE_PITLANE_TEISEJAS = 'pitlane_teisejas';
    const ROLE_TRASOS_TEISEJAS = 'trasos_teisejas';
    const ROLE_RYSININKAS = 'rysininkas';
    const ROLE_SIGNALIZUOTOJAS = 'signalizuotojas';
    const ROLE_FAKTO_TEISEJAS = 'fakto_teisejas';
    const ROLE_LINIJOS_TEISEJAS = 'linijos_teisejas';
    const ROLE_HANDICAPPER = 'handicapper';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Competition
     * @ORM\ManyToOne(targetEntity="Competition", inversedBy="judges")
     */
    private $competition;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="competitions")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="role", type="string", length=255)
     */
    private $role;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Competition
     */
    public function getCompetition()
    {
        return $this->competition;
    }

    /**
     * @param Competition $competition
     */
    public function setCompetition(Competition $competition)
    {
        $this->competition = $competition;
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
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }
}
