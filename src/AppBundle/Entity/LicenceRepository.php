<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class LicenceRepository extends EntityRepository
{
    /**
     * @param array|string $type
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getValidLicences($type)
    {
        $qb = $this->createQueryBuilder('l');
        return $qb->select('l, u')
            ->leftJoin('l.user', 'u')
            ->where($qb->expr()->eq('u.enabled', '1'))
            ->andWhere($qb->expr()->gte('l.expiresAt', ':now'))
            ->andWhere($qb->expr()->in('l.status', ':status'))
            ->andWhere($qb->expr()->in('l.type', ':type'))
            ->setParameters(
                [
                    'now' => (new \DateTime())->format('Y-m-d'),
                    'status' => [Licence::STATUS_PRODUCED, Licence::STATUS_PAID,],
                    'type' => $type,
                ]
            );
    }

    /**
     * @param QueryBuilder $qb
     * @param string $name
     * @return QueryBuilder
     */
    public function filterLicencesByName(QueryBuilder $qb, $name)
    {
        return $qb
            ->andWhere(
                $qb->expr()->like(
                    $qb->expr()->concat('u.firstname',
                    $qb->expr()->concat($qb->expr()->literal(' '), 'u.lastname')), ':name'
                )
            )
            ->setParameter('name', '%' . $name . '%');
    }

    /**
     * @param User $user
     * @param $type
     * @return null|Licence
     */
    public function getReusableLicence(User $user, $type)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select('l')
            ->leftJoin('l.documents', 'd')
            ->where('d.user = :user')
            ->andWhere('d.type = :type')
            ->andWhere('d.status != :status')
            ->addOrderBy('d.validUntil', 'DESC')
            ->addOrderBy('d.updatedAt', 'DESC')
            ->setParameters(
                [
                    'user' => $user,
                    'type' => $type,
                    'status' => Licence::STATUS_DOCUMENT_REJECTED,
                ]);
        $fileUpload = new FileUpload();

        if ($fileUpload->haveDateField($type)) {
            $date = (new \DateTime('now'))->format('Y-m-d');
            if ($fileUpload->isValidThisYear($type)) {
                $date = (new \DateTime('last day of december 00:00:00'))->format('Y-m-d');
            }

            $qb
                ->andWhere('d.validUntil >= :validUntil')
                ->setParameter('validUntil', $date);
        }

        return $qb->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * @param User $user
     * @return QueryBuilder
     */
    public function getLicencesQueryBuilder(User $user)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select('l, u, s, d')
            ->join('l.user', 'u')
            ->leftJoin('l.sports', 's')
            ->leftJoin('l.declarant', 'd')
            ->where('l.status <> :status')
            ->setParameter('status', Licence::STATUS_UNCONFIRMED);

        if (!$user->hasRole('ROLE_ADMIN') && $user->hasRole('ROLE_ACCOUNTANT')) {
            $qb->andWhere('l.status IN (:accountant)')->setParameter('accountant', [Licence::STATUS_NOT_PAID, Licence::STATUS_PAID, Licence::STATUS_INVOICE]);
        }

        return $qb;
    }
}
