<?php namespace AppBundle\Form;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class UserType extends AbstractType
{
    /**
     * @var User
     */
    private $editor;

    public function __construct(User $editor)
    {
        $this->editor = $editor;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $editor = $this->editor;
        $user = $builder->getData();

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
            ->add('birthday', 'birthday', [
                'label' => 'user.label.birthday',
                'years' => range(date('Y') - 0, date('Y') - 100),
                'placeholder' => ['year' => 'user.profile.year', 'month' => 'user.profile.month', 'day' => 'user.profile.day'],
                'format' => 'yyyy MMMM dd',
                'required' => false,
            ])->add('languages', 'entity', [
                'class' => 'AppBundle:Language',
                'multiple' => true,
                'label' => 'user.label.language',
                'choice_label' => 'language',
                'placeholder' => 'licences.placeholder',
            ])
            ->add('secondaryLanguage', 'text', [
                'label' => 'user.language_placeholder',
                'attr' => ['required' => false],
                'data' => $user->getSecondaryLanguage(),
            ])
            ->add('gender', 'choice', [
                'choices' => User::$genderMap,
                'label' => 'user.label.gender',
            ])
            ->add('city', 'text', [
                'label' => 'user.label.city',
                'attr' => ['placeholder' => 'user.city_placeholder']
            ]);

        if ($editor->hasRole('ROLE_ADMIN')) {
            $builder
                ->add('legal', 'checkbox', [
                    'label' => 'user.label.legal',
                    'required' => false,
                ])
                ->add('associated', 'choice', [
                    'label' => 'user.label.associated',
                    'required' => false,
                    'placeholder' => 'user.edit.association_undefined',
                    'choices' => [
                        'user.edit.accociated' => true,
                        'user.edit.not_accociated' => false,
                    ],
                    'choices_as_values' => true,
                ])
                ->add('enabled', 'checkbox', [
                    'label' => 'user.label.enabled',
                    'required' => false,
                ])
                ->add('imageFile', 'file', [
                    'label' => 'user.label.image_file',
                    'required' => false,
                    'attr' => [
                        'class' => 'imgInp'
                    ],
                    'constraints' => [new Image([
                        'maxSize' => '3M',
                    ])],
                    'validation_groups' => ['profile', 'Default']
                ])
                ->add('notes', 'textarea', [
                    'label' => 'user.label.notes',
                    'required' => false,
                    'attr' => [
                        'rows' => 6
                    ]
                ])
                ->add('aboutMe', 'textarea', [
                    'label' => 'user.label.about_me',
                    'required' => false,
                    'attr' => [
                        'rows' => 6
                    ]
                ])
                ->add('plainPassword', 'repeated', [
                    'type' => 'password',
                    'first_options' => ['label' => 'user.label.password'],
                    'second_options' => ['label' => 'user.label.repeat_password'],
                    'required' => false
                ])
                ->add('termsConfirmed', 'checkbox', [
                    'label' => 'user.label.terms',
                    'required' => false
                ])
                ->add('parent', 'entity', [
                    'label' => 'user.label.parent',
                    'class' => User::class,
                    'choice_label' => function(User $user) {
                        return $user->getMemberName() . ' (' . $user->getFullName() . ')';
                    },
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('BIT_AND(u.roles, :role) > 0')
                            ->andWhere('u.enabled = 1')
                            ->andWhere('u.confirmed = 1')
                            ->orderBy('u.memberName')
                            ->setParameter('role', User::$roleMap['ROLE_DECLARANT']);
                    },
                    'placeholder' => 'user.edit.no_declarant',
                    'required' => false,
                ])->add('memberName', 'text', [
                    'label' => 'user.label.member_name',
                    'required' => false,
                ])->add('memberCode', 'text', [
                    'label' => 'user.label.member_code',
                    'required' => false,
                ])->add('vatCode', 'text', [
                    'label' => 'user.label.vat_code',
                    'required' => false,
                ])->add('bank', 'text', [
                    'label' => 'user.label.bank',
                    'required' => false,
                ])->add('bankAccount', 'text', [
                    'label' => 'user.label.bank_account',
                    'required' => false,
                ])->add('phone', 'text', [
                    'label' => 'user.label.phone',
                    'required' => false,
                ])->add('address', 'text', [
                    'label' => 'user.label.address',
                    'required' => false,
                ]);
            $roles = [
                'ROLE_RACER' => 'user.roles.simple_user',
                'ROLE_DECLARANT' => 'user.roles.declarant' ,
                'ROLE_ORGANISATOR' => 'user.roles.organisator',
//                'ROLE_JUDGE' => 'user.roles.judge',
                'ROLE_ADMIN' => 'user.roles.admin',
                'ROLE_DEPARTMENT' => 'user.roles.department',
                'ROLE_ACCOUNTANT' => 'user.roles.accountant',
                'ROLE_CHAIRMAN' => 'user.roles.chairman',
                'ROLE_SPECTATOR' => 'user.roles.spectator',
//                'ROLE_LASF_COMMITTEE' => 'user.roles.lasf_committee',
//                'ROLE_SVO_COMMITTEE' => 'user.roles.svo_committee',
                'ROLE_COMPETITION_CHIEF' => 'user.roles.competition_chief',
//                'ROLE_PRESIDENT' => 'user.roles.president',
                'ROLE_SECRETARY' => 'user.roles.secretary',
//                'ROLE_JUDGE_COMMITTEE' => 'user.roles.judge_committee',
                'ROLE_SKK_HEAD' => 'user.roles.skk_head'
            ];
        } else {
            $roles = [
                'ROLE_RACER' => 'user.roles.racer',
                'ROLE_JUDGE' => 'user.roles.judge',
            ];
        }

        foreach ($roles as $key => $role) {
            $builder->add($key, 'checkbox', [
                'label' => $role,
                'required' => false,
                'mapped' => false
            ]);
            if ($key == User::ROLE_LASF_COMMITTEE) {
                $builder->add('sports', 'entity', [
                    'class' => 'AppBundle:Sport',
                    'label' => 'licences.label.sports',
                    'placeholder' => 'licences.sport_select.placeholder',
                    'choice_label' => 'name',
                    'multiple' => true,
                ]);
            }
        }
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }
}
