<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class CompetitionClassValidator extends ConstraintValidator
{
    public function validate($user, Constraint $constraint)
    {
        if (!$user->getDateFrom() || !$user->getDateTo()) {
            return;
        }

        if ($user->getDateFrom() > $user->getDateTo()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('dateTo')
                ->addViolation();
        }
    }
}
