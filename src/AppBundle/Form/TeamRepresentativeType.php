<?php namespace AppBundle\Form;

use AppBundle\Entity\TeamRepresentative;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TeamRepresentativeType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'tr';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fullName', 'text', [
            'label' => 'team_representative.label.full_name',
            'required' => true,
            'attr' => ['placeholder' => 'team_representative.label.full_name'],
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
            'data_class' => TeamRepresentative::class,
        ]);
    }
}
