<?php namespace AppBundle\Form;

use AppBundle\Entity\DateRestriction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateRestrictionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'date',
            'datetime',
            [
                'label'  => 'date_restriction.label.date',
                'attr'   => [
                    'placeholder' => 'date_restriction.label.placeholder',
                    'class'       => 'date',
                ],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ]
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'date_restriction';
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => DateRestriction::class,
            ]
        );
    }
}
