<?php namespace AppBundle\Form\Type\FileUpload;

use AppBundle\Entity\FileUpload;
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
            'label' => 'file_uploads.reason.title',
            'required' => false,
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FileUpload::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reject';
    }
}
