<?php

namespace AppBundle\Form;

use AppBundle\Entity\Application;
use AppBundle\Entity\User;
use AppBundle\Validator\Constraints\ApplicationClass;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class ApplicationType extends ApplicationBaseType
{
    /**
     * @var User
     */
    private $editor;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * ApplicationType constructor.
     * @param AuthorizationChecker $authorizationChecker
     * @param TokenStorage $tokenStorage
     */
    public function __construct(AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage)
    {
        $this->editor = $tokenStorage->getToken()->getUser();
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $editor = $this->editor;
        /** @var Application $application */
        $application = $builder->getData();
        $lpExpressList = $options['allow_extra_fields'];
        $disabled = $application ? $application->isDisabled(): false;
        $this->buildCommonFields($builder, $this->editor, $disabled);
        $builder
            ->add('lasfAddress', 'text', [
                'label' => 'application.label.lasf_address',
                'required' => true,
                'attr' => [
                    'class' => 'readonly',
                    'oninvalid' => "setCustomValidity('Prašome papildyti savo profilio informaciją \"Profilis\" skiltyje.')",
                ],
            ])
            ->add('phone', 'text', [
                'label' => 'application.label.phone',
                'required' => true,
                'attr' => [
                    'class' => 'readonly',
                    'oninvalid' => "setCustomValidity('Prašome papildyti savo profilio informaciją \"Profilis\" skiltyje.')",
                ],
            ])
            ->add('vatNumber', 'text', [
                'label' => 'application.label.vat_number',
                'required' => false,
                'attr' => ['readonly' => true],
            ])
            ->add('memberCode', 'text', [
                'label' => 'application.label.member_code',
                'required' => false,
                'attr' => ['readonly' => true],
            ])
            ->add('bank', 'text', [
                'label' => 'application.label.bank',
                'required' => false,
                'attr' => ['readonly' => true],
            ])
            ->add('bankAccount', 'text', [
                'label' => 'application.label.bank_account',
                'required' => false,
                'attr' => ['readonly' => true],
            ])
            ->add('lasfEmail', 'text', [
                'label' => 'application.label.lasf_email',
                'required' => true,
                'attr' => ['readonly' => true],
            ])
            ->add('city', 'hidden', ['required' => false])
            ->add('state', 'hidden', ['required' => false])
            ->add('street', 'hidden', ['required' => false])
            ->add('country', 'hidden', ['required' => false])
            ->add('zip_code', 'hidden', ['required' => false])
            ->add('street_number', 'hidden', ['required' => false])
            ->add('application_copy', 'file', [
                'label' => 'application.label.file',
                'required' => false,
                'multiple' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'btn-file',
                    'accept' => 'png|gif|jpg|jpeg|pdf|doc|docx',
                ],
            ])
            ->add('lasfName', 'text', [
                'label' => 'application.label.lasf_name',
                'required' => true,
                'attr' => ['class' => 'readonly'],
            ]);

        if ($this->authorizationChecker->isGranted('additional-fields-view', $editor)
            && !$application->isTermsConfirmed()
            && !$application->getName()
        ) {
            $builder
                ->add('termsConfirmed', 'checkbox', [
                    'label' => 'user.label.terms',
                    'required' => false,
                    'attr' => ['class' => 'hidden'],
                ])
                ->add('termsConfirmed', 'checkbox', [
                    'label' => 'user.label.terms',
                    'required' => true,
                ]);
        }

        if ($this->authorizationChecker->isGranted('application-new', $editor) && !$application->getName()) {
            $builder->add('lasfName', 'entity', [
                'class' => 'AppBundle:User',
                'label' => 'application.label.lasf_name',
                'required' => false,
                'multiple' => false,
                'empty_value' => 'application.label.not_selected',
                'choice_label' => 'memberName',
                'attr' => [
                    'class' => 'select2',
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->select(['u'])
                        ->where('BIT_AND(u.roles, :role_org) > 0')
                        ->orWhere('BIT_AND(u.roles, :role_dec) > 0')
                        ->setParameter('role_org', User::$roleMap['ROLE_ORGANISATOR'])
                        ->setParameter('role_dec', User::$roleMap['ROLE_DECLARANT']);
                },
            ]);
        }

        if ($this->authorizationChecker->isGranted('delivery-modify', $editor) && $application->isUnconfirmed()) {
            $builder
                ->add('deliverTo', 'choice', [
                    'label' => 'application.label.deliver_to',
                    'choices' => [
                        '0' => 'application.label.lasf',
                        '1' => 'application.label.lp_express',
                    ],
                    'expanded' => true,
                    'multiple' => false,
                ])
                ->add('deliverToAddress', 'choice',
                    [
                        'choices' => $lpExpressList,
                        'label' => 'application.label.deliver_to_address',
                        'required' => true,
                        'attr' => [
                            'placeholder' => 'application.label.deliver_to_address',
                        ],
                    ]);
        }

        if (!$application->getId()) {
            $builder->add('subCompetitions', 'collection', [
                'label' => false,
                'type' => new SubCompetitionType($this->authorizationChecker, $this->tokenStorage),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
            ]);
        }

        if ($application->getId()) {

            if (($application->isConfirmed()
                    || $application->isLasfUploadedContract()
                    || $application->isOrganisatorUploadedContract()
                    || $application->isNotPaid()
                    || $application->isPaid()
                )
                && $this->authorizationChecker->isGranted('agreement-upload', $editor)
            ) {
                $builder->add('contractUrl', 'text', [
                    'label' => 'application.label.contract_by_lasf_url',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'application.label.contract_by_lasf_url',
                    ],
                ]);
            }

            if (($application->isOrganisatorUploadedContract() || $application->isNotPaid())
                && $this->authorizationChecker->isGranted('invoice-upload', $editor)
                && !$application->getDocumentsByType('invoice')
            ) {
                $this->buildDocument($builder, 'invoice');
            }

            if (($application->hasAgreementStatus()
                && !$application->getDocumentsByType(Application::STATUS_CONTRACT_UPLOADED_BY_LASF))
                && $this->authorizationChecker->isGranted('agreement-upload', $editor)
            ) {
                $this->buildDocument($builder, 'signed_application_by_lasf', 1);
            }

            if (($application->hasAgreementStatus()
                && !$application->getDocumentsByType(Application::STATUS_CONTRACT_UPLOADED_BY_ORGANISATOR))
                && $this->authorizationChecker->isGranted('organiser-signed-agreement-upload', $editor)
                && ($application->getUser() == $this->editor || $this->editor->hasRole(User::ROLE_ADMIN))
            ) {
                $this->buildDocument($builder, 'signed_application_by_organisator', 1);
            }

            if ($application->hasAgreementStatus()) {

                if ($this->authorizationChecker->isGranted('insurance-upload', $editor)
                    && !$application->getDocumentsByType('competition_insurance')
                ) {
                    $this->buildDocument($builder, 'competition_insurance', true);
                }

                if ($this->authorizationChecker->isGranted('other-documents-view', $editor)) {
                    $this->buildDocument($builder, 'other_documents', false);
                }

                if ($this->authorizationChecker->isGranted('additional-regulations-upload', $editor)) {
                    $this->buildDocument($builder, 'additional_rules', false);
                }

                if ($this->authorizationChecker->isGranted('track-license-upload', $editor)) {
                    $this->buildDocument($builder, 'track_licence', false);
                }

                if ($this->authorizationChecker->isGranted('track-acceptance-upload', $editor)) {
                    $this->buildDocument($builder, 'track_acceptance', false);
                }

                if ($this->authorizationChecker->isGranted('safety-plan-upload', $editor)) {
                    $this->buildDocument($builder, 'safety_plan', false);
                }

                if ($this->authorizationChecker->isGranted('safety-plan-view', $editor)) {
                    $disabledInspection = $this->authorizationChecker->isGranted('safety-plan-inspection-date-edit', $editor);
                    $builder
                        ->add('inspectionDate', 'datetime', [
                            'label' => 'application.label.inspection_date',
                            'required' => false,
                            'attr' => [
                                'placeholder' => 'application.label.inspection_date',
                                'autocomplete' => 'off',
                                'class' => $disabled || !$disabledInspection ? 'date no-pointer' : 'datetime',
                            ],
                            'widget' => 'single_text',
                            'format' => 'YYYY-MM-dd HH:mm',
                            'read_only' => $disabled || !$disabledInspection,
                        ]);
                }
                if ($this->authorizationChecker->isGranted('organiser-license-upload', $editor)
                    && !$application->getDocumentsByType('organisator_licence')
                ) {
                    $this->buildDocument($builder, 'organisator_licence', true);
                }

                $this->competitionDelegates($application, $builder, $this->authorizationChecker, $editor, $application->isDisabled());

                if ($application->isPaid()) {
                    if (date('Y-m-d') >= $application->getDateTo()->format('Y-m-d')) {
                        $this->competitionResultDocuments($application, $builder, $this->authorizationChecker, $editor);
                    }
                }
            }
        }
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'application';
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Application::class,
                'cascade_validation' => true,
            ]
        );
    }
}
