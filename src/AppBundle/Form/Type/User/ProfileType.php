<?php namespace AppBundle\Form\Type\User;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProfileType extends AbstractType
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('aboutMe', 'textarea', [
                'required' => false,
                'label' => 'user.label.about_me',
                'attr' => ['rows' => 10],
            ])
            ->add('phone', 'text', [
                'required' => false,
                'label' => 'user.label.phone',
            ])
            ->add('address', 'text', [
                'required' => false,
                'label' => 'user.label.address',
            ])
            ->add('plainPassword', 'repeated', [
                'type' => 'password',
                'invalid_message' => 'user.profile.password_mismatch',
                'required' => false,
                'first_options'  => ['label' => 'user.label.password'],
                'second_options' => ['label' => 'user.label.repeat_password'],
            ])
            ->add('imageFile', 'file', [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'imgInp'
                ],
                'constraints' => [new Image([
                    'maxSize' => '3M',
                ])],
            ])
            ->add('receiveNotifications', 'checkbox', [
                'label' => 'user.label.receive_notifications',
                'required' => false,
            ]);

        if (!$this->user->isLegal()) {
            if (!$this->user->getBirthday()) {
                $builder->add('birthday', 'birthday', [
                    'label' => 'user.label.birthday',
                    'placeholder' => ['year' => 'user.profile.year', 'month' => 'user.profile.month', 'day' => 'user.profile.day'],
                    'format' => 'yyyy MMMM dd',
                    'required' => false,
                ]);
            }
            $builder->add('identityCode', 'text', [
                'required' => false,
                'label' => 'user.label.id_code',
            ]);
            $builder
                ->add('languages', 'entity', [
                    'class' => 'AppBundle:Language',
                    'multiple' => true,
                    'label' => 'user.label.language',
                    'choice_label' => 'language',
                    'placeholder' => 'licences.placeholder',
                ])
                ->add('secondaryLanguage', 'text', [
                    'label' => 'user.language_placeholder',
                    'attr' => ['required' => false],
                    'data' => $this->user->getSecondaryLanguage(),
                ])
                ->add('gender', 'choice', [
                    'choices' => User::$genderMap,
                    'label' => 'user.label.gender',
                ])
                ->add('city', 'text', [
                    'label' => 'user.label.city',
                    'attr' => ['placeholder' => 'user.city_placeholder']
                ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User',
            'intention' => 'profile',
        ]);
    }

    public function getName()
    {
        return 'profile';
    }
}
