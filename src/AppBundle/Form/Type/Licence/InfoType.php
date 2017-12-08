<?php namespace AppBundle\Form\Type\Licence;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use AppBundle\Entity\UserInfo;
use AppBundle\EventListener\Licence\InsuranceFieldListener;
use AppBundle\EventListener\Licence\LicencePreSubmitSubscriber;
use AppBundle\Form\DocumentType;
use AppBundle\Form\TeamRepresentativeType;
use AppBundle\Form\Type\User\DeclarantUserType;
use AppBundle\Lasf\UrgencyChoices;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class InfoType extends AbstractType
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Licence
     */
    private $licence;

    /**
     * @var bool
     */
    private $editing;

    /**
     * @var UrgencyChoices
     */
    private $urgencyChoices;

    /**
     * @param User $user
     * @param Licence $licence
     * @param UrgencyChoices $urgencyChoices
     * @param bool $editing
     */
    public function __construct(User $user, Licence $licence, UrgencyChoices $urgencyChoices, $editing = false)
    {
        $this->user = $user;
        $this->licence = $licence;
        $this->editing = $editing;
        $this->urgencyChoices = $urgencyChoices;

        if ($licence->getStatus() != Licence::STATUS_UNCONFIRMED) {
            return;
        }

        foreach ($licence->getDocumentsTypes() as $documentsType) {
            $licence->addDocument(new FileUpload($documentsType));
        }
    }

    public function getName()
    {
        return 'licence_info';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lpExpressList = $options['allow_extra_fields'];
        $user = $this->user;
        $licence = $this->licence;

        if (!$this->editing) {
            $builder->add('documents', 'collection', [
                'by_reference' => false,
                'type' => new DocumentType($user, true, $licence),
                'label' => false,
            ]);
        }

        if ($this->urgencyChoices) {
            $builder->add('urgency', 'choice', [
                'label' => 'licences.label.urgency',
                'attr' => ['class' => 'urgency'],
                'choices' => [
                    Licence::URGENCY_STANDARD => $this->urgencyChoices->getChoiceTitle(Licence::URGENCY_STANDARD),
                    Licence::URGENCY_URGENT => $this->urgencyChoices->getChoiceTitle(Licence::URGENCY_URGENT),
                ],
                'choices_as_values' => false,
                'expanded' => true,
                'constraints' => [new NotNull()],
                'required' => true,
            ]);
        }

        if (!$licence->isMembershipLicence()) {
            $builder->add('email', 'email', [
                'label' => 'licences.label.judge.email',
                'read_only' => $licence->isDeclarantLicence() ? true : false,
                'constraints' => [new NotBlank(),],
            ]);

            $builder->add('city', 'text', [
                'label' => 'user.label.city',
                'attr' => ['placeholder' => 'user.city_placeholder']
            ]);
        }

        if ($licence->isMembershipLicence()) {
            $builder->add('fullName', 'text', [
                'label' => 'user.label.fullname',
                'mapped' => false,
                'read_only' => true,
                'required' => false,
            ]);
            $builder->add('memberName', 'text', [
                'label' => 'user.label.member_name',
                'mapped' => false,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('memberCode', 'text', [
                'label' => 'user.label.member_code',
                'mapped' => false,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('vatCode', 'text', [
                'label' => 'user.label.vat_code',
                'mapped' => false,
                'required' => false,
            ]);
            $builder->add('bank', 'text', [
                'label' => 'user.label.bank',
                'mapped' => false,
                'required' => false,
            ]);
            $builder->add('bankAccount', 'text', [
                'label' => 'user.label.bank_account',
                'mapped' => false,
                'required' => false,
            ]);
            $builder->add('address', 'text', [
                'label' => 'user.label.address',
                'mapped' => false,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('phone', 'text', [
                'label' => 'user.label.phone',
                'mapped' => false,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('personalCode', 'choice', [ // reuse field, is associated
                'label' => 'user.label.associated',
                'choices' => [
                    'user.edit.not_accociated' => 0,
                    'user.edit.accociated' => 1,
                ],
                'choices_as_values' => true,
                'constraints' => [new NotNull()],
            ]);
        }

        if ($licence->isDeclarantLicence()) {
            $builder->add('lasfName', 'text', [
                'label' => 'licences.label.declarant.lasf_name',
                'read_only' => true,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('teamName', 'text', [
                'label' => 'licences.label.declarant.team_name'
            ]);
            $builder->add('personalCode', 'text', [
                'label' => 'licences.label.declarant.personal_code',
                'read_only' => true,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('vatNumber', 'text', [
                'label' => 'licences.label.declarant.vat_number',
                'required' => false,
                'read_only' => true,
            ]);
            $builder->add('lasfAddress', 'text', [
                'label' => 'licences.label.declarant.lasf_address',
                'read_only' => true,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('mobileNumber', 'text', [
                'label' => 'licences.label.declarant.mobile_number',
                'read_only' => true,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('managerFullName', 'text', [
                'label' => 'licences.label.declarant.manager_full_name',
                'read_only' => true,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('representatives', 'collection', [
                'label' => 'licences.label.representatives',
                'type' => new TeamRepresentativeType(),
                'by_reference' => false,
                'allow_add' => true,
                'required' => false,
            ]);

            $builder->add('users', 'collection', [
                'label' => 'licences.label.users',
                'type' => new DeclarantUserType(),
                'mapped' => false,
                'allow_add' => true,
                'required' => false,
            ]);
        }

        if ($licence->isDriverLicence()) {
            $builder->add('lasfName', 'text', [
                'label' => 'licences.label.declarant.lasf_name',
                'read_only' => true,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('firstDriver', 'checkbox', [
                'label' => 'licences.label.driver.first_driver',
                'label_attr' => ['class' => 'control-label required bold-text'],
            ]);
            $builder->add('secondDriver', 'checkbox', [
                'label' => 'licences.label.driver.second_driver',
                'label_attr' => ['class' => 'control-label required bold-text'],
            ]);
            $builder->add('lasfAddress', 'text', [
                'label' => 'licences.label.declarant.lasf_address',
                'read_only' => true,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('mobileNumber', 'text', [
                'label' => 'licences.label.declarant.mobile_number'
            ]);
            $builder->add('personalCode', 'text', [
                'label' => 'licences.label.declarant.personal_code',
                'read_only' => true,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('licence', 'entity', [
                'label' => 'licences.label.licence',
                'attr' => ['class' => 'licence'],
                'class' => Licence::class,
                'choice_label' => 'teamName',
                'group_by' => 'user.memberName',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    $qb = $er->createQueryBuilder('l')
                        ->join('l.user', 'u')
                        ->join('u.members', 'um')
                        ->where('l.type IN(:types)')
                        ->andWhere('l.status IN(:statuses)')
                        ->andWhere('um.id = :myId')
                        ->andWhere('l.expiresAt >= :now')
                        ->andWhere('u.enabled = 1')
                        ->setParameter('now', (new \DateTime())->format('Y-m-d'))
                        ->setParameter('types', Licence::$declarantTypes)
                        ->setParameter('statuses', array_merge(Licence::$completedStatuses, [Licence::STATUS_NOT_PAID, Licence::STATUS_INVOICE]))
                        ->setParameter('myId', $user->getId());

                    return $qb;
                },
                'constraints' => [new NotBlank()],
                'placeholder' => 'licences.info.select_team',
            ]);
            $builder->add('sports', 'entity', [
                'class' => 'AppBundle:Sport',
                'label' => 'licences.label.sports',
                'required' => true,
                'placeholder' => 'licences.sport_select.placeholder',
                'choice_label' => 'name',
                'multiple' => true,

            ]);
        }

        if ($this->isType(Licence::TYPE_ORGANISATOR)) {
            $builder->add('name', 'text', [
                'label' => 'licences.label.organisator.name'
            ]);
            $builder->add('date', 'datetime', [
                'label' => 'licences.label.organisator.date',
                'attr' => ['class' => 'date'],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ]);
            $builder->add('lasfName', 'text', [
                'label' => 'licences.label.declarant.lasf_name',
                'read_only' => true,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('lasfAddress', 'text', [
                'label' => 'licences.label.organisator.lasf_address',
                'read_only' => true,
                'constraints' => [new NotBlank()],

            ]);
            $builder->add('phone', 'text', [
                'label' => 'licences.label.organisator.phone',
                'read_only' => true,
                'constraints' => [new NotBlank()],
            ]);
            $builder->add('vatNumber', 'text', [
                'label' => 'licences.label.declarant.vat_number',
                'required' => false,
                'read_only' => true,

            ]);
            $builder->add('bank', 'text', [
                'label' => 'licences.label.organisator.bank',
                'required' => false,
                'read_only' => true,
            ]);
            $builder->add('bankAccount', 'text', [
                'label' => 'licences.label.organisator.bank_account',
                'required' => false,
                'read_only' => true,
            ]);
            $builder->add('mobileNumber', 'text', [
                'label' => 'licences.label.organisator.mobile_number'
            ]);
            $builder->add('managerFullName', 'text', [
                'label' => 'licences.label.declarant.manager_full_name'
            ]);
            $builder->remove('documents');
        }

        if ($licence->isJudgeLicence()) {
            $builder->add('declarant', 'entity', [
                'label' => 'licences.label.judge.declarant',
                'class' => User::class,
                'choice_label' => 'memberName',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.id = :declarant')
                        ->andWhere('u.enabled = 1')
                        ->andWhere('u.confirmed = 1')
                        ->setParameter('declarant', $this->user->getParent() ? $this->user->getParent() : 0);
                },
                'constraints' => [
                    new NotBlank()
                ]
            ]);
            $builder->add('name', 'text', [
                'label' => 'licences.label.judge.name'
            ]);
            $builder->add('lasfAddress', 'text', [
                'label' => 'licences.label.judge.lasf_address'
            ]);
            $builder->add('mobileNumber', 'text', [
                'label' => 'licences.label.judge.mobile_number'
            ]);
            $builder->add('sports', 'entity', [
                'class' => 'AppBundle:Sport',
                'label' => 'licences.label.sports',
                'placeholder' => 'licences.sport_select.placeholder',
                'choice_label' => 'name',
                'multiple' => true,

            ]);
        }

        if ($licence->isJudgeLicence() || $licence->isDriverLicence()) {
            $builder->add('gender', 'choice', [
                'choices' => User::$genderMap,
                'label' => 'user.label.gender',
            ]);
            $builder->add('languages', 'entity', [
                'class' => 'AppBundle:Language',
                'multiple' => true,
                'label' => 'user.label.language',
                'choice_label' => 'language',
                'placeholder' => 'licences.placeholder',
            ]);
            $builder->add('secondaryLanguage', 'text', [
                'label' => 'user.language_placeholder',
                'attr' => ['required' => false],
                'data' => $user->getSecondaryLanguage() ? : ' ',
            ]);
            $builder->add('date', 'datetime', [
                'label' => 'licences.label.judge.date',
                'attr' => ['class' => 'date'],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ]);
            $builder->add('deliverTo', 'hidden', [
                'constraints' => [new NotBlank()],
            ]);
            $builder
                ->add('deliverTo', 'choice', [
                    'label' => false,
                    'attr' => ['class' => 'deliverTo'],
                    'choices' => [
                        '0' => 'application.label.lasf',
                        '1' => 'application.label.lp_express',
                    ],
                    'expanded' => true,
                    'multiple' => false,
                ])
                ->add('deliverToAddress', 'choice', [
                    'choices' => $lpExpressList,
                    'label' => 'application.label.deliver_to_address',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'application.label.deliver_to_address',
                    ],
                ])->add('deliver', 'hidden', ['mapped' => false]); // field added only to pass validation

            if ($licence->isLasfInsurance()) {
                $builder
                    ->add('identityCode', 'text', [
                        'label' => 'licences.label.insurance.code',
                        'required' => true,
                    ]);
            }
            if ($licence->getStatus() == Licence::STATUS_UNCONFIRMED) {
                $builder
                    ->add('identityCode', 'text', [
                        'label' => 'licences.label.insurance.code',
                        'required' => false,
                    ])
                    ->add('lasfInsurance', 'checkbox', [
                        'label' => 'licences.label.insurance.agree',
                        'label_attr' => ['class' => 'control-label bold-text'],
                        'required' => false
                    ]);
            }
        }

        $builder->add('licenceType', 'hidden', ['mapped' => false, 'data' => $licence->getType(),]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($licence) {
            /** @var Licence $data */
            $data = $event->getData();

            if ($licence->isMembershipLicence() && $data['personalCode'] == 1) {
                unset($data['documents']);
                $event->getForm()->remove('documents');
            }
        });

        $builder->addEventSubscriber(new InsuranceFieldListener());
        $builder->addEventSubscriber(new LicencePreSubmitSubscriber());
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Licence::class,
        ]);
    }

    private function isType($type)
    {
        return $this->licence->getType() == $type;
    }

}
