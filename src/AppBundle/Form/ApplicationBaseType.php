<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use AppBundle\Entity\User;
use AppBundle\Entity\SubCompetition;
use AppBundle\Entity\Application;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\File;

abstract class ApplicationBaseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param User $user
     * @param bool $disabled
     */
    protected function buildCommonFields(FormBuilderInterface $builder, User $user, $disabled)
    {
        $builder
            ->add('dateFrom', 'datetime', [
                'label' => 'application.label.date_from',
                'required' => true,
                'attr' => [
                    'placeholder' => 'application.label.date_start',
                    'autocomplete' => 'off',
                    'class' => $disabled ? 'date no-pointer' : 'date',
                ],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'read_only' => $disabled,
            ])
            ->add('dateTo', 'datetime', [
                'label' => 'application.label.date_to',
                'required' => true,
                'attr' => [
                    'placeholder' => 'application.label.date_end',
                    'autocomplete' => 'off',
                    'class' => $disabled ? 'date no-pointer' : 'date',
                ],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'read_only' => $disabled,
            ])
            ->add('name', 'text', [
                'label' => 'application.label.competition_name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'application.label.placeholder',
                ],
            ])
            ->add('sport', 'entity', [
                'class' => 'AppBundle:Sport',
                'label' => 'application.label.sport',
                'required' => true,
                'placeholder' => 'application.sport_select.placeholder',
                'choice_label' => 'name',
            ])
            ->add('stage', 'choice', [
                'label' => 'application.label.stage',
                'required' => true,
                'placeholder' => 'application.stage_select.placeholder',
                'choices' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                    'once' => 'application.stage_select.once',
                ],
            ])
            ->add('location', 'text', [
                'label' => 'application.label.location',
                'required' => true,
                'attr' => [
                    'placeholder' => 'application.label.location_placeholder',
                ],
            ])
            ->add('type', 'choice', [
                'label' => 'application.label.type',
                'placeholder' => 'application.type_select.placeholder',
                'required' => true,
                'choices' => [
                    'championship' => 'application.type_select.championship',
                    'priority' => 'application.type_select.priority',
                ],
            ])
            ->add('league', 'choice', [
                'label' => 'application.label.league',
                'required' => true,
                'placeholder' => 'application.league_select.placeholder',
                'choices' => [
                    'exist' => 'Yra',
                    'none' => 'Nėra',
                ],
            ]);
    }

    /**
     * @param SubCompetition|Application $competition
     * @param FormBuilderInterface $builder
     * @param AuthorizationChecker $authorization
     * @param User $user
     * @param bool $disabled
     */
    protected function competitionDelegates($competition, FormBuilderInterface $builder, AuthorizationChecker $authorization, User $user, $disabled) {
        /** Saving sport of subCompetition or application */
        $sport = $competition->getSport();
        if ($authorization->isGranted('competition-chief-view', $user)) {
            $builder
                ->add('competitionChief', 'entity', [
                    'class' => 'AppBundle:CompetitionChief',
                    'label' => 'application.label.competition_chief',
                    'required' => false,
                    'placeholder' => 'application.competition_chief_select.placeholder',
                    'choice_label' => 'user.fullName',
                    'disabled' => $disabled,
                    'query_builder' => function (EntityRepository $er) use ($sport) {
                        $qb = $er->createQueryBuilder('cc');
                        if ($sport) {
                            $qb->select(['cc', 's'])
                                ->leftJoin('cc.sports', 's')
                                ->where('s.id = :sport')
                                ->setParameter('sport', $sport->getId());
                        }
                        return $qb;
                    },
                ])
                ->add('competitionChiefConfirmed', 'choice', [
                    'label' => 'application.label.competition_chief_confirmed_choice',
                    'required' => false,
                    'placeholder' => 'application.competition_chief_confirmed_select.placeholder',
                    'disabled' => $disabled,
                    'choices' => [
                        0 => 'application.competition_chief_assigned.state.no',
                        1 => 'application.competition_chief_assigned.state.yes',
                    ],
                ]);
        }

        if (($competition->isCompetitionChief($user) && $authorization->isGranted(['judge-assign-chief'], $user))
                || $authorization->isGranted(['judge-assign-organiser'], $user)) {
            $builder->add('judges', 'entity', [
                'class' => 'AppBundle:User',
                'label' => 'application.label.judges',
                'required' => false,
                'placeholder' => 'application.judge_select.placeholder',
                'disabled' => $disabled,
                'choice_label' => 'fullName',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->select(['u'])
                        ->where('BIT_AND(u.roles, :role) > 0')
                        ->setParameter('role', User::$roleMap['ROLE_JUDGE']);
                },
                'multiple' => true,
            ]);
        }

        if ($authorization->isGranted('commissioner-select', $user)) {
            $builder->add('stewards', 'entity', [
                'class' => 'AppBundle:Steward',
                'label' => 'application.label.steward',
                'required' => false,
                'placeholder' => 'application.steward.placeholder',
                'disabled' => $disabled,
                'choice_label' => 'user.fullName',
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) use ($sport) {
                    $qb = $er->createQueryBuilder('s');
                    if ($sport) {
                        $qb->select(['s', 'ss'])
                            ->leftJoin('s.sports', 'ss')
                            ->where('ss.id = :sport')
                            ->setParameter('sport', $sport->getId());
                    }
                    return $qb;
                },
            ]);
        }

        if ($authorization->isGranted('lasf-spectator-select', $user)) {
            $builder->add('observer', 'entity', [
                'class' => 'AppBundle:User',
                'label' => 'application.label.observer',
                'required' => false,
                'placeholder' => 'application.observer.placeholder',
                'disabled' => $disabled,
                'choice_label' => 'fullName',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->select(['u'])
                        ->where('BIT_AND(u.roles, :role) > 0')
                        ->setParameter('role', User::$roleMap['ROLE_SPECTATOR']);
                },
            ]);
        }

        if ($authorization->isGranted('safety-chief-select', $user)) {
            $builder->add('safetyChief', 'entity', [
                'class' => 'AppBundle:SafetyChief',
                'label' => 'application.label.safety_chief',
                'empty_value' => 'application.label.not_selected',
                'disabled' => $disabled,
                'choice_label' => 'fullname',
                'multiple' => false,
                'required' => false,
            ]);
        }

        if ($authorization->isGranted('lasf-technician-select', $user)) {
            $builder->add('technicalDelegate', 'text', [
                'label' => 'application.label.technical_delegate',
                'disabled' => $disabled,
                'required' => false,
                'attr' => [
                    'placeholder' => 'application.technical_delegate.placeholder',
                ],
            ]);
        }

        if ($authorization->isGranted('svo-delegate-select', $user)) {
            $builder->add('svoDelegate', 'entity', [
                'class' => 'AppBundle:User',
                'label' => 'application.label.svo_delegate',
                'disabled' => $disabled,
                'required' => false,
                'placeholder' => 'application.svo_delegate.placeholder',
                'choice_label' => 'fullName',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->select(['u'])
                        ->where('BIT_AND(u.roles, :role) > 0')
                        ->setParameter('role', User::$roleMap['ROLE_SVO_COMMITTEE']);
                },
            ]);
        }

        if ($authorization->isGranted('skk-chairman-select', $user)) {
            $builder->add('skkHead', 'entity', [
                'class' => 'AppBundle:User',
                'label' => 'application.label.skk_head',
                'required' => false,
                'placeholder' => 'application.skk_head.placeholder',
                'disabled' => $disabled,
                'choice_label' => 'fullName',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->select(['u'])
                        ->where('BIT_AND(u.roles, :role) > 0')
                        ->setParameter('role', User::$roleMap['ROLE_SKK_HEAD']);
                },
            ]);
        }
    }

    /**
     * @param Application $application
     * @param FormBuilderInterface $builder
     * @param AuthorizationChecker $authorization
     * @param User $user
     */
    protected function competitionResultDocuments($application, FormBuilderInterface $builder, AuthorizationChecker $authorization, User $user) {

        $documentsMaps = [
            'result-upload' => 'competition_results',
            'report-upload' => 'report',
            'bulletin-upload' => 'bulletin',
            'skk-chairman-report-upload' => 'skk_report',
            'competition-chief-decision-upload' => 'competition_chief_decisions',

        ];

        foreach ($documentsMaps as $key => $value)
            if ($authorization->isGranted($key, $user) && !$application->getDocumentsByType($value)) {
                $this->buildDocument($builder, $value);
        }
    }

    /**
     * @param FormBuilderInterface $builder
     * @param $type
     * @param bool $single
     */
    protected function buildDocument(FormBuilderInterface $builder, $type, $single = false) {
        $options = [
            'class' => 'btn-file',
            'accept' => 'gif|jpg|jpeg|pdf|png|doc|docx',
        ];

        if ($single) {
            $options['maxlength'] = 1;
        }

        $builder->add($type, 'file', [
            'label' => 'application.label.'.$type,
            'required' => false,
            'multiple' => true,
            'mapped' => false,
            'attr' => $options,
        ]);
    }

    protected function getLeagueChoices(User $user)
    {
        $choices = [
            'a' => 'application.league_select.a',
            'b' => 'application.league_select.b',
            'c' => 'application.league_select.c',
            'other' => 'application.league_select.other',
        ];

        if (!$user->isAssociated()) {
            $choices = ['a' => 'application.league_select.a'] + $choices;
        }

        return $choices;
    }
}
