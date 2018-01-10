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
}
