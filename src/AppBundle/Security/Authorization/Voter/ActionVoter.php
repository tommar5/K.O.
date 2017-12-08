<?php

namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserInterface;

class ActionVoter extends AbstractVoter
{
    /**
     * ActionVoter constructor
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    private $actions             = [];
    private $supportedAttributes = [];

    /**
     * Get supported attributes - actions from roleAction Entity.
     *
     * @return array
     */
    protected function getSupportedAttributes()
    {
        if (empty($this->supportedAttributes)) {
            $all = $this->em->getRepository('AppBundle:RoleAction')->findAll();

            foreach ($all as $item) {
                $this->supportedAttributes[] = $item;
            }
        }

        foreach ($this->supportedAttributes as $action) {
            $attributes[] = $action->getAction();
        }

        return $attributes;
    }

    /**
     * Get supported classes
     *
     * @return array
     */
    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\User'];
    }

    /**
     * Check if permission is granted
     *
     * @param string $attribute
     * @param object $object
     * @param null   $user
     *
     * @return bool
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user instanceof User) {
            throw new \LogicException('The user is somehow not our User class!');
        }

        if (empty($this->actions)) {
            $all = $this->em->getRepository('AppBundle:RoleAction')->findAll();

            foreach ($all as $item) {
                $this->actions[] = $item;
            }
        }

        foreach ($this->actions as $load) {
            if ($load->getAction() === $attribute) {
                $granted = $load;
            }
        }

        if ($granted->isActionAllowed($user->getRoles())) {
            return true;
        }

        return false;
    }
}
