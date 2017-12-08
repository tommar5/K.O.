<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Application;
use AppBundle\Entity\FileUpload;
use AppBundle\Entity\SubCompetition;
use AppBundle\Entity\TimeRestriction;
use Doctrine\ORM\EntityManager;

class ApplicationExtension extends \Twig_Extension
{
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('calculate_terms', [$this, 'calculateTerms']),
        ];
    }

    /**
     * @param SubCompetition|Application $application
     * @param FileUpload $document
     * @param string $type
     * @return string
     */
    public function calculateTerms($application, FileUpload $document, $type)
    {
        $legalTimes = $this->em->getRepository(TimeRestriction::class)->find(1);

        $documentCreatedAt = clone $document->getCreatedAt();
        $applicationCreatedAt = clone $application->getCreatedAt();
        $applicationDateFrom = clone $application->getDateFrom();
        $documentCreatedAt->setTime(0, 0);
        $applicationCreatedAt->setTime(0, 0);
        $applicationDateFrom->setTime(0, 0);

        if ($legalTimes->{'getIs' . $type}() && $legalTimes->{'get' . $type}() != '') {
            return $documentCreatedAt->diff($applicationDateFrom)->format("%r%a") - $legalTimes->{'get' . $type}();
        } else {
            return $legalTimes->{'get' . $type}() - $applicationCreatedAt->diff($documentCreatedAt)->format("%r%a");
        }
    }

    public function getName()
    {
        return 'application_extension';
    }
}
