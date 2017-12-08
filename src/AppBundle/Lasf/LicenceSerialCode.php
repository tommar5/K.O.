<?php namespace AppBundle\Lasf;

use AppBundle\Entity\Licence;
use Doctrine\ORM\EntityManager;

class LicenceSerialCode
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createSerialNumber(Licence $licence)
    {
        $licence->setSerialNumber($this->getSerialNumber($licence));

        if ($this->hasPermanentNumbering($licence) || $licence->isMembershipLicence()) {
            $licence->setSeries(Licence::$seriesNames[$licence->getType()]);
        } else {
            $licence->setSeries($this->getYear($licence) . '-' . Licence::$seriesNames[$licence->getType()]);
        }
    }

    private function hasPermanentNumbering(Licence $licence)
    {
        return $licence->isMembershipLicence();
    }

    private function getSerialNumber(Licence $licence)
    {
        $maxNumber = $this->em->getRepository(Licence::class)->createQueryBuilder('l')->select('MAX(l.serialNumber)');

        if ($licence->isMembershipLicence()) {
            $maxNumber->where('l.type = :type')->setParameter('type', Licence::TYPE_MEMBERSHIP);
        } else {
            if ($licence->isJudgeLicence()) {
                $types = Licence::$judgeTypes;
            } elseif ($licence->isDeclarantLicence()) {
                $types = Licence::$declarantTypes;
            } elseif ($licence->isDriverLicence()) {
                $types = Licence::$driverTypes;
            } elseif ($licence->isOrganisatorLicence()) {
                $types = Licence::$organisatorTypes;
            } else {
                $type = $licence->getType();
                $maxNumber
                    ->where('l.type = :type')
                    ->setParameter('type', $type);
            }
            if (isset($types)) {
                $maxNumber
                    ->where('l.type IN(:types)')
                    ->setParameter('types', $types);
            }
            $maxNumber
                ->andWhere('l.expiresAt LIKE :year')
                ->setParameter('year', $this->getYear($licence) . '-%');
        }

        $max = (int)$maxNumber->getQuery()->getSingleScalarResult();

        return $max + 1;
    }

    /**
     * @param Licence $licence
     * @return string
     */
    private function getYear(Licence $licence)
    {
        return $licence->getExpiresAt()->format('Y');
    }
}
