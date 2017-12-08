<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class BirthdayRequiredValidator extends ConstraintValidator
{
    public function validate($user, Constraint $constraint)
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isLegal() && is_null($user->getBirthday())) {
            $this->context->buildViolation($constraint->message)
                ->atPath('birthday')
                ->addViolation();
        }
    }
}
