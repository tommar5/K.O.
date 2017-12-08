<?php
namespace AppBundle\EventListener\Traits;

use AppBundle\Entity\Sport;
use AppBundle\Entity\User;

trait RecipientTrait
{

    /**
     * @param $roles
     * @param null|Sport $sport
     * @return array
     */
    private function getUsersByRoles(array $roles, $sport = null)
    {
        $users = $this->em->getRepository(User::class)->createQueryBuilder('u');

        foreach ($roles as $role) {
            if (User::$roleMap['ROLE_LASF_COMMITTEE'] == $role) {
                $users->orWhere('BIT_AND(u.roles, :srole) > 0 AND :sport MEMBER OF u.sports')
                    ->setParameter('srole', $role)
                    ->setParameter('sport', $sport);
            } else {
                $users->orWhere('BIT_AND(u.roles, :role' . $role . ') > 0')
                    ->setParameter('role' . $role, $role);
            }
        }

        return $users->getQuery()->getResult();
    }
}
