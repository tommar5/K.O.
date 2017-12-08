<?php namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsLegalName extends Constraint
{
    public $message = 'application.name';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}