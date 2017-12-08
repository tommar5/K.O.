<?php namespace AppBundle\Form;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompetitionType extends AbstractType
{
    /**
     * @var User
     */
    private $user;

    /**
     * CompetitionType constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'competition';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('licence', 'entity', [
                'label' => 'competition.label.licence',
                'class' => Licence::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->where('l.status IN(:status)')
                        ->andWhere('l.expiresAt >= :now')
                        ->andWhere('l.type = :type')
                        ->andWhere('l.user = :user')
                        ->setParameter('now', (new \DateTime())->format('Y-m-d'))
                        ->setParameter('status', Licence::$completedStatuses)
                        ->setParameter('type', Licence::TYPE_ORGANISATOR)
                        ->setParameter('user', $this->user);
                },
                'placeholder' => 'competition.new.choose_licence',
                'choice_label' => function(Licence $e) {
                    return $e->getName() . ' (' . $e->getDate()->format('Y-m-d') . ')';
                },
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('name', 'text', [
                'label' => 'competition.label.name'
            ])
            ->add('location', 'text', [
                'label' => 'competition.label.location'
            ])
            ->add('mainJudge', 'entity', [
                'label' => 'competition.label.main_judge',
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('BIT_AND(u.roles, :role) > 0')
                        ->andWhere('u.enabled = 1')
                        ->andWhere('u.confirmed = 1')
                        ->setParameter('role', User::$roleMap['ROLE_JUDGE']);
                },
                'placeholder' => 'competition.new.choose_judge',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('dateFrom', 'datetime', [
                'label' => 'competition.label.date_from',
                'attr' => ['class' => 'datetime'],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm',
            ])
            ->add('dateTo', 'datetime', [
                'label' => 'competition.label.date_to',
                'attr' => ['class' => 'datetime'],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm',
                'required' => false,
            ])
            ->add('description', 'textarea', [
                'label' => 'competition.label.description',
                'required' => false,
            ])
            ->add('watcher', 'text', [
                'label' => 'competition.label.watcher',
                'required' => false,
            ])
            ->add('safetyWatcher', 'text', [
                'label' => 'competition.label.safety_watcher',
                'required' => false,
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
            'data_class' => Competition::class,
        ]);
    }
}
