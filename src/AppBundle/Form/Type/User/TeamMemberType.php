<?php namespace AppBundle\Form\Type\User;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TeamMemberType extends AbstractType
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->user;

        $builder->add('member', 'entity', [
            'class' => User::class,
            'mapped' => false,
            'label' => 'user.label.fullname',
            'query_builder' => function (EntityRepository $er) use ($user) {

                $members = $user->getMembers()->map(function ($member) {
                    return $member->getId();
                })->toArray();

                $qb = $er->createQueryBuilder('u')
                    ->where('BIT_AND(u.roles, :role) > 0')
                    ->andWhere('u.confirmed = 1')
                    ->setParameter('role', User::$roleMap['ROLE_RACER']);

                if (sizeof($members) > 0) {
                    $qb->andWhere('u.id NOT IN(:m)')->setParameter('m', $members);
                }

                return $qb;
            },
            'constraints' => [
                new NotBlank(),
            ]
        ]);
    }

    public function getName()
    {
        return 'team_member';
    }
}
