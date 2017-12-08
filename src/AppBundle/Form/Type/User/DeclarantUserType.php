<?php

namespace AppBundle\Form\Type\User;

use AppBundle\Entity\User;
use AppBundle\Form\Type\Licence\TypeSelectType;
use AppBundle\Validator\Constraints\UserIsNatural;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeclarantUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', [
                'label' => 'user.label.email',
                'attr' => ['placeholder' => 'user.label.email'],
                'constraints' => [new UserIsNatural()],
            ])
            ->add('firstname', 'text', [
                'label' => 'user.label.firstname',
                'attr' => ['placeholder' => 'user.label.firstname']
            ])
            ->add('lastname', 'text', [
                'label' => 'user.label.lastname',
                'attr' => ['placeholder' => 'user.label.lastname']
            ])
            ->add('prefLicence', 'choice', [
                'label' => 'user.label.pref_licence',
                'choices' => (new TypeSelectType())->getDriverLicenceTypes(),
            ])
            ->add('city', 'text', [
                'label' => 'user.label.city',
                'attr' => ['placeholder' => 'user.city_placeholder']
             ])
            ->add('gender', 'choice', [
                'choices' => User::$genderMap,
                'label' => 'user.label.gender',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function getName()
    {
        return 'du';
    }
}
