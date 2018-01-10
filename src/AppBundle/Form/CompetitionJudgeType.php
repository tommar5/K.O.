<?php namespace AppBundle\Form;

use AppBundle\Entity\CompetitionJudge;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompetitionJudgeType extends AbstractType
{
    private $mainJudge;

    public function __construct(User $mainJudge)
    {
        $this->mainJudge = $mainJudge;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'judges';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', 'entity', [
            'label' => 'competition_judge.label.user',
            'class' => User::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('BIT_AND(u.roles, :role) > 0')
                    ->andWhere('u.id != :mainJudge')
                    ->andWhere('u.enabled = 1')
                    ->andWhere('u.confirmed = 1')
                    ->setParameters([
                        'role' => User::$roleMap['ROLE_JUDGE'],
                        'mainJudge' => $this->mainJudge->getId()
                    ]);
            },
            'constraints' => [
                new NotBlank()
            ]
        ]);
        $builder->add('role', 'choice', [
            'label' => 'competition_judge.label.role',
            'choices' => [
                CompetitionJudge::ROLE_SPORTO_KOMISARAS => 'competition_judge.role.koncerto_komisaras',
                CompetitionJudge::ROLE_VARZYBU_VADOVAS => 'competition_judge.role.koncerto_vadovas',
                CompetitionJudge::ROLE_VARZYBU_SEKTORIUS => 'competition_judge.role.koncerto_sektorius',
                CompetitionJudge::ROLE_LAIKININKAS => 'competition_judge.role.laikininkas',
                CompetitionJudge::ROLE_TECHNINIS_KOMISARAS => 'competition_judge.role.techninis_komisaras',
                CompetitionJudge::ROLE_TECHNINIS_TEISEJAS => 'competition_judge.role.techninis_vadovas',
                CompetitionJudge::ROLE_SAUGUMO_VIRSININKAS => 'competition_judge.role.saugumo_virsininkas',
                CompetitionJudge::ROLE_PITLANE_TEISEJAS => 'competition_judge.role.pitlane_teisejas',
                CompetitionJudge::ROLE_TRASOS_TEISEJAS => 'competition_judge.role.trasos_teisejas',
                CompetitionJudge::ROLE_RYSININKAS => 'competition_judge.role.rysininkas',
                CompetitionJudge::ROLE_SIGNALIZUOTOJAS => 'competition_judge.role.signalizuotojas',
                CompetitionJudge::ROLE_FAKTO_TEISEJAS => 'competition_judge.role.fakto_teisejas',
                CompetitionJudge::ROLE_LINIJOS_TEISEJAS => 'competition_judge.role.linijos_teisejas',
                CompetitionJudge::ROLE_HANDICAPPER => 'competition_judge.role.handicapper',
            ],
            'constraints' => [
                new NotBlank()
            ]
        ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompetitionJudge::class,
        ]);
    }


}
