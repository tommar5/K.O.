<?php namespace AppBundle\Lasf;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class Registrator
{
    /**
     * @var EncoderFactory
     */
    private $encoderFactory;

    public function __construct(EncoderFactory $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function registerUser(User $registrator, User $user)
    {
        $this->updateUser($user);

        if ($registrator->hasRole('ROLE_DECLARANT')) {
            $user->setParent($registrator);

            if ($user->hasRole('ROLE_RACER')) {
                $registrator->addMember($user);
            }
        }
    }

    public function editingUser(User $user)
    {
        $this->updateUser($user);

        if (!$user->hasRole('ROLE_RACER')) {
            foreach ($user->getDeclarants() as $declarant) {
                $declarant->removeMember($user);
            }
        }
    }

    public function updateUser(User $user)
    {
        $this->limitRolesByLegalStatus($user);
        $this->changeUserPassword($user);
    }

    public function createRandomString($length = 8)
    {
        $chars = "0123456789abcdefghijklmnopqrstuvwxyz";

        if ($length > strlen($chars)) {
            throw new \RuntimeException('This function cannot generate strings that long.');
        }

        return substr(str_shuffle($chars), 0, $length);
    }

    /**
     * Changes user password to one set in planPassword
     * @param User $user
     */
    private function changeUserPassword(User $user)
    {
        if ($user->getPlainPassword()) {
            $user->setPassword(
                $this->encoderFactory->getEncoder($user)->encodePassword($user->getPlainPassword(), $user->getSalt())
            );
        }
    }

    /**
     * @param $user
     */
    private function limitRolesByLegalStatus(User $user)
    {
        if ($user->isLegal()) {
            $user->removeRole('ROLE_JUDGE');
            $user->removeRole('ROLE_RACER');
        } else {
            $user->removeRole('ROLE_ORGANISATOR');
            $user->removeRole('ROLE_DECLARANT');
            $user->setAssociated(null);
        }
    }
}
