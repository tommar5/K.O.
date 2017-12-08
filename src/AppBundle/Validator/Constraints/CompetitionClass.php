<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CompetitionClass extends Constraint
{
    public $message = 'competition.date_to';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}