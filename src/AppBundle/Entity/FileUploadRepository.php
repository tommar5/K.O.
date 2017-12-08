<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class FileUploadRepository
 * @package AppBundle\Entity
 */
class FileUploadRepository extends EntityRepository
{
    /**
     * @param $fileName
     * @return mixed
     */
    public function fileExist($fileName)
    {
        return $this->createQueryBuilder('d')
            ->select('d')
            ->where('d.fileName = :name')
            ->setParameter('name', $fileName)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}
