<?php

namespace AppBundle\Twig;

use AppBundle\Entity\SubCompetition;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

class SubCompetitionExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('checkForSports', [$this, 'checkForSports']),
        ];
    }

    /**
     * @param SubCompetition $subCompetitions
     * @param User $user
     * @return bool
     */
    public function checkForSports($subCompetitions, User $user)
    {
        /**
         * @var SubCompetition $competition
         */
        if ($user->hasRole(User::ROLE_LASF_COMMITTEE)) {
            if ($subCompetitions instanceof PersistentCollection) {
                $sports = new ArrayCollection();
                foreach ($subCompetitions as $competition) {
                    if ($user->hasSport($competition->getSport())) {
                        $sports->add($competition->getSport());
                    }
                }
                return !$sports->isEmpty();
            } else {
                return $user->hasSport($subCompetitions->getSport());
            }
        }
        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sub_competition_extension';
    }
}
