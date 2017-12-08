<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 * @package AppBundle\Entity
 */
class UserRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param Licence $licence
     * @return User|null
     */
    public function getTeamMembersWithLicence(User $user, Licence $licence)
    {
        return $this->createQueryBuilder('q')
            ->select('q, m, l')
            ->leftJoin('q.children', 'm')
            ->leftJoin('m.licences', 'l')
            ->where('q = :user')
            ->andWhere('l = :licence')
            ->setParameter('user', $user)
            ->setParameter('licence', $licence)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return User[]
     */
    public function getAdmins()
    {
        return $this->createQueryBuilder('u')
            ->where('BIT_AND(u.roles, :role) > 0')
            ->setParameter('role', User::$roleMap['ROLE_ADMIN'])
            ->getQuery()->getResult();
    }
}
