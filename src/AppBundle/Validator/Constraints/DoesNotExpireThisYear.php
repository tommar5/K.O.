<?php namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DoesNotExpireThisYear extends Constraint
{
    public $message = 'does_not_expire.too_soon';
}
