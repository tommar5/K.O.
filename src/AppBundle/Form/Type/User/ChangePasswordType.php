<?php namespace AppBundle\Form\Type\User;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', 'repeated', [
                'type' => 'password',
                'invalid_message' => 'user.profile.password_mismatch',
                'first_options'  => ['label' => 'user.label.password'],
                'second_options' => ['label' => 'user.label.repeat_password'],
                'constraints' => [
                    new Length(['min' => 8]),
                    new NotBlank()
                ]
            ]);

        if (!$this->user->isLegal()) {
            if (!$this->user->getBirthday()) {
                $builder->add('birthday', 'birthday', [
                    'label' => 'user.label.birthday',
                    'years' => range(date('Y') - 0, date('Y') - 100),
                    'placeholder' => ['year' => 'user.profile.year', 'month' => 'user.profile.month', 'day' => 'user.profile.day'],
                    'format' => 'yyyy MMMM dd',
                    'constraints' => [
                        new NotBlank()
                    ]
                ]);
            }
            $builder->add('phone', 'text', [
                'label' => 'user.label.phone',
                'constraints' => [
                    new NotBlank()
                ]
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
        ]);
    }

    public function getName()
    {
        return 'change_password';
    }
}
