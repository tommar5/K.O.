<?php namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class UserIsNaturalValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }

        if ($user = $this->em->getRepository(User::class)->findOneBy(['email' => trim($value)])) {
            if ($user->isLegal()) {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        }
    }
}
