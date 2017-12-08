<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ApplicationClass extends Constraint
{
    public $message = 'application.date_to';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}