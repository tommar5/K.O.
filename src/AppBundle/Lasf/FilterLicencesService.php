<?php
namespace AppBundle\Lasf;

use AppBundle\Entity\Licence;
use DataDog\PagerBundle\Pagination;
use Doctrine\ORM\QueryBuilder;

class FilterLicencesService
{

    /**
     * @param QueryBuilder $qb
     * @param string $key
     * @param string $val
     * @return QueryBuilder
     */
    public function licencesFilter(QueryBuilder $qb, $key, $val)
    {
        if (empty($val) || $val == Pagination::$filterAny) {
            return $qb;
        }

        switch ($key) {
            case 'l.id':
                $qb->andWhere('l.id = :id');
                $qb->setParameter('id', $val);
                break;
            case 'l.fullName':
                $qb->andWhere($qb->expr()->like(
                    $qb->expr()->concat('u.firstname', $qb->expr()->concat($qb->expr()->literal(' '), 'u.lastname')),
                    ':uname'
                ));
                $qb->setParameter('uname', "%$val%");
                break;
            case 'serial':
                $qb->andWhere($qb->expr()->like(
                    $qb->expr()->concat('l.series', $qb->expr()->concat($qb->expr()->literal('/'), 'l.serialNumber')),
                    ':serial'
                ));
                $qb->setParameter('serial', "%$val%");
                break;
            case 'l.type':
                $qb->andWhere($qb->expr()->like('l.type', ':type'));
                $qb->setParameter('type', "%$val%");
                break;
            case 'l.createdAt':
                $qb->andWhere($qb->expr()->like('l.createdAt', ':ca'));
                $qb->setParameter('ca', "%$val%");
                break;
            case 'l.expiresAt':
                $qb->andWhere($qb->expr()->like('l.expiresAt', ':ea'));
                $qb->setParameter('ea', "%$val%");
                break;
            case 'l.status':
                $qb->andWhere($qb->expr()->like('l.status', ':s'));
                $qb->setParameter('s', $val);
                break;
            case 'legal':
                $qb->leftJoin('l.declarant', 'ld');
                $qb->leftJoin('l.user', 'lu');
                $qb->leftJoin('l.licence', 'll');
                $qb->leftJoin('ll.user', 'llu');
                $qb->andWhere($qb->expr()->orX('ld.memberName LIKE :memberName', 'ld.memberName LIKE :memberName', 'llu.memberName LIKE :memberName', 'lu.memberName LIKE :memberName'));
                $qb->setParameter('memberName', '%'.$val.'%');
                break;
            case 'l.city':
                $qb->andWhere($qb->expr()->like('l.city', ':city'));
                $qb->setParameter('city', "%$val%");
                break;
            case 'l.language':
                $qb->leftJoin('l.languages', 'lg');
                $qb->andWhere($qb->expr()->like('lg.language', ':language'));
                $qb->setParameter('language', "%$val%");
                break;
            case 'u.secondaryLanguage':
                $qb->andWhere($qb->expr()->like('l.secondaryLanguage', ':secondaryLanguage'));
                $qb->setParameter('secondaryLanguage', "%$val%");
                break;
            case 'l.sports':
                $qb->leftJoin('l.sports', 'sp');
                $qb->andWhere('sp.id = :sport');
                $qb->setParameter('sport', $val);
                break;
            case 'l.gender':
                $qb->andWhere('l.gender = :gender');
                $qb->setParameter('gender', $val);
                break;
            case 'l.driverTypes':
                switch ($val) {
                    case Licence::FIRST_DRIVER:
                        $qb->andWhere('l.firstDriver = 1');
                        break;
                    case Licence::SECOND_DRIVER:
                        $qb->andWhere('l.secondDriver = 1');
                        break;
                }
        }

        return $qb;
    }
}
