<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BirthdayRequired extends Constraint
{
    public $message = 'birthday_required.required';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
