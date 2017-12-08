<?php namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserIsNatural extends Constraint
{
    public $message = 'user_is_natural.legal';

    public function validatedBy()
    {
        return 'natural_validator';
    }
}
