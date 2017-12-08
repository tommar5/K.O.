<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class ApplicationRepository
 * @package AppBundle\Entity
 */
class ApplicationRepository extends EntityRepository
{
    /**
     * @param User $user
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllApplications(User $user)
    {
        $qb = $this->createQueryBuilder('t');
        if (!$user->canSeeAllApplications()) {
            $qb
                ->where('t.user = :user')
                ->setParameter('user', $user);
        }
        return $qb;
    }

    /**
     * @param User $user
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getLasfComiteeApplications(User $user)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->addSelect('s, sub')
            ->leftJoin('t.sport', 's')
            ->leftJoin('t.subCompetitions', 'sub');
        foreach ($user->getSports() as $key => $sport) {
            $qb
                ->orWhere('t.sport = :sport' . $key)
                ->orWhere('sub.sport = :sport' . $key)
                ->setParameter('sport' . $key, $sport);
        }

        return $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOrganisatorApplications(User $user)
    {
        $select = $this->createQueryBuilder('t');
        return $select
            ->where($select->expr()->eq('t.user', ':user'))
            ->setParameter('user', $user);
    }

    /**
     * @param User $user
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getApplicationsByRole(User $user)
    {
        $applications = $this->getAllApplications($user);

        if ($user->hasRole(User::ROLE_LASF_COMMITTEE)) {
            $applications = $this->getLasfComiteeApplications($user);
        }

        if ($user->hasRole(User::ROLE_ORGANISATOR)) {
            $applications = $this->getOrganisatorApplications($user);
        }

        return $applications;
    }

    /**
     * @param int $days
     * @param array $status
     * @return array
     */
    public function getApplicationsThatBeginsBefore($days, $status)
    {
        $em = $this->createQueryBuilder('a');
        return $em->where($em->expr()->andX(
                $em->expr()->eq('DATE_DIFF(a.dateFrom, CURRENT_DATE())', ':DAYS'),
                $em->expr()->in('a.status', ':status')
            ))
            ->setParameter('DAYS', $days)
            ->setParameter('status', $status)
            ->getQuery()->getResult();
    }

}
