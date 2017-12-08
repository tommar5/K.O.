<?php namespace AppBundle\Form\Type\Application;

use AppBundle\Entity\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RejectType
 *
 * @package AppBundle\Form
 */
class ApplicationRejectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'reason',
            'textarea',
            [
                'label'    => 'application.decline.label',
                'required' => true,
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Application::class,
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reject_application';
    }
}
