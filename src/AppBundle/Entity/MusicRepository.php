<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class MusicRepository
 * @package AppBundle\Entity
 */
class MusicRepository extends EntityRepository {

    public function getSongs(){
        return $this->createQueryBuilder('m')
            ->getQuery();
    }

    public function getUserSongs($userId){
        return $this->createQueryBuilder('m')
            ->leftJoin('m.users', 'fs')
            ->where('fs.id=:id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->getResult();
    }
}
