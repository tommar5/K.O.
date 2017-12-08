<?php namespace AppBundle\Form;

use AppBundle\Entity\Sport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SportType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'sport_type';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $object = $builder->getData();

        $aliasOptions = [
            'label' => 'sport.label.alias',
        ];

        if ($object->getId()) {
            $aliasOptions['attr'] = [
                'readonly' => true,
            ];
        }

        $builder
            ->add('alias', 'text', $aliasOptions)
            ->add('name', 'text', [
                'label' => 'sport.label.sport',
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
            'data_class' => Sport::class
        ]);
    }


}