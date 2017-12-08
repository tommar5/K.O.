<?php namespace AppBundle\Form\Type\Competition;

use AppBundle\Entity\CompetitionParticipant;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticipantType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'participants';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', 'entity', [
            'label' => 'user.label.fullname',
            'class' => User::class,
            'placeholder' => 'competition_participant.add.select_user',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('BIT_AND(u.roles, :role) > 0')
                    ->andWhere('u.enabled = 1')
                    ->andWhere('u.confirmed = 1')
                    ->setParameter('role', User::$roleMap['ROLE_RACER']);
            },
            'constraints' => [
                new NotBlank()
            ]
        ]);

        $builder->add('points', 'number', [
            'label' => 'competition_participant.label.points',
            'precision' => 2,
            'constraints' => [
                new NotBlank()
            ]
        ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompetitionParticipant::class,
        ]);
    }
}
