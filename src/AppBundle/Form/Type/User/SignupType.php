<?php

namespace AppBundle\Form\Type\User;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

class SignupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', [
                'label' => 'user.label.email',
            ])
            ->add('firstname', 'text', [
                'label' => 'user.label.firstname',
            ])
            ->add('lastname', 'text', [
                'label' => 'user.label.lastname',
            ])
            ->add('legal', 'checkbox', [
                'label' => 'user.label.legal',
                'required' => false,
            ])
            ->add('memberName', 'text', [
                'label' => 'user.label.member_name',
            ])
            ->add('memberCode', 'text', [
                'label' => 'user.label.member_code',
                'required' => false,
            ])
            ->add('address', 'text', [
                'required' => false,
                'label' => 'user.label.address',
            ])
            ->add('city', 'text', [
                'label' => 'user.label.city',
                'attr' => ['placeholder' => 'user.city_placeholder']
            ])
            ->add('gender', 'choice', [
                'choices' => User::$genderMap,
                'label' => 'user.label.gender',
            ])
            ->add('languages', 'entity', [
                'class' => 'AppBundle:Language',
                'multiple' => true,
                'label' => 'user.label.language',
                'choice_label' => 'language',
                'placeholder' => 'licences.placeholder',
            ])
            ->add('secondaryLanguage', 'text', [
                'label' => 'user.language_placeholder',
                'attr' => ['required' => false]
            ])
            ->add('captcha', 'ewz_recaptcha', [
                'label' => 'captcha',
                'mapped' => false,
                'constraints' => [
                    new RecaptchaTrue(['message' => 'user.reset.invalid_captcha']),
                ],
            ]);

        $roles = [
            'ROLE_RACER' => 'user.roles.racer',
            'ROLE_DECLARANT' => 'user.roles.declarant' ,
            'ROLE_ORGANISATOR' => 'user.roles.organisator',
            'ROLE_JUDGE' => 'user.roles.judge',
        ];

        foreach ($roles as $key => $role) {
            $builder->add($key, 'checkbox', [
                'label' => $role,
                'required' => false,
                'mapped' => false
            ]);
        }
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
        return 'user';
    }
}
