<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class ApplicationClassValidator extends ConstraintValidator
{
    public function validate($application, Constraint $constraint)
    {
        if (!$application->getDateFrom() || !$application->getDateTo()) {
            return;
        }

        if ($application->getDateFrom() > $application->getDateTo()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('dateTo')
                ->addViolation();
        }
    }
}
