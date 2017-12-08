<?php namespace AppBundle\Form\Type\Licence;

use AppBundle\Entity\Licence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RejectType
 * @package AppBundle\Form
 */
class RejectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('reason', 'textarea', [
            'label' => 'licences.decline.licence',
            'required' => true,
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Licence::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reject_licence';
    }
}
