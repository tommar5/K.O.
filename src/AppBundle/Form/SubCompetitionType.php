<?php

namespace AppBundle\Form;

use AppBundle\Entity\SubCompetition;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use AppBundle\Entity\User;

class SubCompetitionType extends ApplicationBaseType
{

    /**
     * @var AuthorizationChecker $authorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var User $user
     */
    private $user;

    /**
     * SubCompetitionType constructor.
     * @param AuthorizationChecker $authorizationChecker
     * @param TokenStorage $tokenStorage
     */
    public function __construct(AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $competition = $builder->getData();
        $disabled = $competition ? $competition->isDisabled(): false;
        $this->buildCommonFields($builder, $this->user, $disabled);

        if ($competition) {
            $builder
                ->add('lasfAddress', 'text', [
                    'label' => 'application.label.lasf_address',
                    'mapped' => false,
                    'required' => true,
                    'attr' => [
                        'class' => 'readonly',
                        'oninvalid' => "setCustomValidity('Prašome papildyti savo profilio informaciją \"Profilis\" skiltyje.')",
                    ],
                    'data' => $competition->getApplication()->getlasfAddress(),
                ])
                ->add('phone', 'text', [
                    'label' => 'application.label.phone',
                    'mapped' => false,
                    'required' => true,
                    'attr' => [
                        'class' => 'readonly',
                        'oninvalid' => "setCustomValidity('Prašome papildyti savo profilio informaciją \"Profilis\" skiltyje.')",
                    ],
                    'data' => $competition->getApplication()->getPhone(),
                ])
                ->add('vatNumber', 'text', [
                    'label' => 'application.label.vat_number',
                    'mapped' => false,
                    'required' => false,
                    'attr' => ['readonly' => true],
                    'data' => $competition->getApplication()->getVatNumber(),
                ])
                ->add('memberCode', 'text', [
                    'label' => 'application.label.member_code',
                    'mapped' => false,
                    'required' => false,
                    'attr' => ['readonly' => true],
                    'data' => $competition->getApplication()->getMemberCode(),
                ])
                ->add('bank', 'text', [
                    'label' => 'application.label.bank',
                    'mapped' => false,
                    'required' => false,
                    'attr' => ['readonly' => true],
                    'data' => $competition->getApplication()->getBank(),
                ])
                ->add('bankAccount', 'text', [
                    'label' => 'application.label.bank_account',
                    'mapped' => false,
                    'required' => false,
                    'attr' => ['readonly' => true],
                    'data' => $competition->getApplication()->getBankAccount(),
                ])
                ->add('lasfName', 'text', [
                    'label' => 'application.label.lasf_name',
                    'mapped' => false,
                    'required' => true,
                    'attr' => ['readonly' => true],
                    'data' => $competition->getApplication()->getLasfName(),
                ])
                ->add('lasfEmail', 'text', [
                    'label' => 'application.label.lasf_email',
                    'mapped' => false,
                    'required' => true,
                    'attr' => ['readonly' => true],
                    'data' => $competition->getApplication()->getLasfEmail(),
                ]);

            if ($competition->getApplication()->hasAgreementStatus()) {
                $this->competitionPrepDocuments($builder);
                if ($this->authorizationChecker->isGranted('safety-plan-view', $this->user)) {
                    $disabledInspection = $this->authorizationChecker->isGranted('safety-plan-inspection-date-edit', $this->user);
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
                $this->competitionDelegates($competition, $builder, $this->authorizationChecker, $this->user, $competition->isDisabled());
                if ($competition->getApplication()->isPaid()) {
                    if (date('Y-m-d') >= $competition->getDateTo()->format('Y-m-d')) {
                        $this->competitionResultDocuments($competition, $builder,
                            $this->authorizationChecker, $this->user);
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
        return 'sub_application';
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SubCompetition::class,
            'cascade_validation' => true,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function competitionPrepDocuments(FormBuilderInterface $builder)
    {
        if ($this->authorizationChecker->isGranted('other-documents-view', $this->user)) {
            $this->buildDocument($builder, 'other_documents');
        }

        if ($this->authorizationChecker->isGranted('additional-regulations-upload', $this->user)) {
            $this->buildDocument($builder, 'additional_rules');
        }

        if ($this->authorizationChecker->isGranted('safety-plan-upload', $this->user)) {
            $this->buildDocument($builder, 'safety_plan', false);
        }
    }
}
