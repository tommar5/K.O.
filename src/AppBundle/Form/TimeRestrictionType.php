<?php

namespace AppBundle\Form;

use AppBundle\Entity\TimeRestriction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeRestrictionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('payTaxesTerm', 'text', ['label' => false, 'required' => false])
            ->add('additionalRulesTerm', 'text', ['label' => false, 'required' => false])
            ->add('additionalRulesConfirmationTerm', 'text', ['label' => false, 'required' => false])
            ->add('finalRulesTerm', 'text', ['label' => false, 'required' => false])
            ->add('safetyPlanTerm', 'text', ['label' => false, 'required' => false])
            ->add('contactAdministrationTerm', 'text', ['label' => false, 'required' => false])
            ->add('trackActTerm', 'text', ['label' => false, 'required' => false])
            ->add('isPayTaxesTerm', 'checkbox', ['label' => false, 'required' => false])
            ->add('isAdditionalRulesTerm', 'checkbox', ['label' => false, 'required' => false])
            ->add('isAdditionalRulesConfirmationTerm', 'checkbox', ['label' => false, 'required' => false])
            ->add('isFinalRulesTerm', 'checkbox', ['label' => false, 'required' => false])
            ->add('IsSafetyPlanTerm', 'checkbox', ['label' => false, 'required' => false])
            ->add('isContactAdministrationTerm', 'checkbox', ['label' => false, 'required' => false])
            ->add('isTrackActTerm', 'checkbox', ['label' => false, 'required' => false]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => TimeRestriction::class,
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'time_restriction';
    }
}
