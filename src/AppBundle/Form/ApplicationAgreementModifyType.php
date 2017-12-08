<?php

namespace AppBundle\Form;

use AppBundle\Entity\CmsBlock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationAgreementModifyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('alias', 'text', ['label' => 'cms_block.label.alias']);
        $builder->add('name', 'text', ['label' => 'cms_block.label.name']);
        $builder->add('content', 'textarea', [
            'label' => 'application_agreement.label.content',
            'required' => true,
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'application_agreement_modify';
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsBlock::class,
        ]);
    }
}
