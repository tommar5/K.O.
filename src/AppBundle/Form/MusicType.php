<?php
namespace AppBundle\Form;

use AppBundle\Entity\Music;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MusicType extends AbstractType
{
    public function getName()
    {
        return 'music_type';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('artist', 'text')
            ->add('name', 'text');
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Music::class
        ]);
    }
}
