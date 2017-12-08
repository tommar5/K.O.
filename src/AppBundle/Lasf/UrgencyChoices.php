<?php

namespace AppBundle\Lasf;

use Doctrine\ORM\EntityManager;

class UrgencyChoices
{
    /**
     * @var EntityManager $em
     */
    private $cmsRepository;

    /**
     * @param $cmsRepository
     */
    public function __construct($cmsRepository)
    {
        $this->cmsRepository = $cmsRepository;
    }

    /**
     * @param string $type
     * @return string|null
     */
    public function getChoiceTitle($type)
    {
        $choice = $this->cmsRepository->findOneBy(['alias' => 'urgency_' . $type]);

        return $choice ? $choice->getContent() : null;
    }
}
