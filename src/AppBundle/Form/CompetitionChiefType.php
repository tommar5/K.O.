<?php namespace AppBundle\Form;

use AppBundle\Entity\CompetitionChief;
use AppBundle\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompetitionChiefType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', 'entity', [
                'class' => 'AppBundle:User',
                'label' => 'competition_chief.label.user',
                'required' => true,
                'placeholder' => 'competition_chief.user_select.placeholder',
                'choice_label' => 'fullName',
                'constraints' => [
                    new NotBlank()
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->select(['u'])
                        ->where('BIT_AND(u.roles, :role) > 0')
                        ->setParameter('role', User::$roleMap['ROLE_COMPETITION_CHIEF']);
                },
            ])
            ->add('sports', 'entity', [
                'class' => 'AppBundle:Sport',
                'label' => 'competition_chief.label.sports',
                'required' => true,
                'placeholder' => 'competition_chief.sport_select.placeholder',
                'choice_label' => 'name',
                'multiple' => true
            ]);
        }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'competition_chief';
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompetitionChief::class,
        ]);
    }
}
