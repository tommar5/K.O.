<?php

namespace AppBundle\Twig;

use AppBundle\Entity\SubCompetition;
use AppBundle\Entity\TimeRestriction;
use Doctrine\ORM\EntityManager;

class TermsCalculationExtention extends \Twig_Extension
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
            new \Twig_SimpleFunction('terms_calculation', [$this, 'competitionTermsCalculation']),
        ];
    }

    /**
     * @param SubCompetition $competition
     * @param string $type
     * @return string
     */
    public function competitionTermsCalculation(SubCompetition $competition, $type)
    {
        /** @var TimeRestriction $legalTimes */
        $legalTimes = $this->em->getRepository(TimeRestriction::class)->createQueryBuilder('t')
            ->getQuery()->getSingleResult();

        $today = new \DateTime();
        $dateFromDiff = $today->diff($competition->getDateFrom());
        $createdDiff = $today->diff($competition->getCreatedAt());
        $confirmationTerm = null;
        if (method_exists($legalTimes, 'get'.$type.'ConfirmationTerm()')) {
            $confirmationTerm = $legalTimes->{'get' . $type . 'ConfirmationTerm'}();
        }
        $term = $legalTimes->{'get'.$type.'Term'}() - 1; // removing additional day for todays calculations

        if ($legalTimes->{'getIs'.$type.'Term'}() && $confirmationTerm) {
            $diff = $createdDiff->days - $term;
        } else {
            $diff = $term - $dateFromDiff->days;

        }

        return $diff;
    }

    public function getName()
    {
        return 'terms_calculation_extension';
    }
}
