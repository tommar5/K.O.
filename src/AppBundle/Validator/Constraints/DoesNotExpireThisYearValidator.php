<?php namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class DoesNotExpireThisYearValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof \DateTime) {
            return;
        }

        if ((new \DateTime('last day of december 00:00:00'))->format('U') > $value->format('U')) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
