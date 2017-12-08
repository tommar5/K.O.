<?php namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsLegalNameValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (strpos(strtolower($value), 'tarptaut') !== false
            || strpos(strtolower($value), 'europos') !== false
            || strpos(strtolower($value), 'pasaul') !== false){
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
                ->addViolation();
        }
    }
}
