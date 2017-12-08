<?php namespace AppBundle\Form\Type\Licence;

use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use DataDog\PagerBundle\Pagination;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeSelectType extends AbstractType
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    public function getName()
    {
        return 'type_select';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$this->user) {
            throw new \InvalidArgumentException('User must be defined to build form.');
        }

        $choices = [];
        $askedForMembership = $this->user->hasAskedForMembership();
//
//        if ($admin) {
//            $choices += [
//                Licence::TYPE_TRACK => 'licences.type.track_licence',
//                Licence::TYPE_SAFETY => 'licences.type.safety_licence',
//            ];
//        }

        if ($this->user->isLegal() && !$this->user->hasActiveMembership()) {
            $this->addLegalLicences($choices);
        }

        if ($this->user->hasRole('ROLE_RACER')) {
            $this->addDriverLicences($choices);

            if ($this->user->hasActiveDLicence()) {
                unset($choices['licences.type.driver'][Licence::TYPE_DRIVER_D]);
            } else {
                unset($choices['licences.type.driver'][Licence::TYPE_DRIVER_D2]);
            }
        }

        if ($this->user->hasRole('ROLE_ORGANISATOR') && $askedForMembership) {
            $this->addOrganisatorChoices($choices);
        }

        if ($this->user->hasRole('ROLE_DECLARANT') && $askedForMembership) {
            $this->addDeclarantChoices($choices);
        }

        if ($this->user->hasRole('ROLE_JUDGE')) {
            $this->addJudgeChoices($choices);
        }

        $builder->add('type', 'choice', [
            'label' => 'licences.label.type',
            'choices' => $choices,
        ]);
    }

    public function getTypesForFilter($includeAny = true)
    {
        $types = $includeAny ? [Pagination::$filterAny => 'all'] : [];

        $this->addLegalLicences($types);
        $this->addDeclarantChoices($types);
        $this->addTypeGroupChoices($types);

        if (!$this->user || $this->user->hasRole('ROLE_ADMIN')) {
            $this->addOrganisatorChoices($types);
            $this->addJudgeChoices($types);
        }

        $this->addDriverLicences($types);

        return $types;
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

    public function getAvailableTypesForChange(Licence $licence)
    {
        $choices = [];

        if ($licence->isDriverLicence()) {
            $this->addDriverLicences($choices);
        }

        if ($licence->isJudgeLicence()) {
            $this->addJudgeChoices($choices);
        }

        if ($licence->isDeclarantLicence()) {
            $this->addDeclarantChoices($choices);
        }

        return $choices;
    }

    public function getDriverLicenceTypes()
    {
        $c = [];
        $this->addDriverLicences($c);

        return $c;
    }

    private function addTypeGroupChoices(&$choices)
    {
        $choices += [
            'driver' => 'licences.type.driver',
            'declarant' => 'licences.type.declarant',
            'judge' => 'licences.type.judge',
        ];
    }

    /**
     * @param $choices
     * @return array
     */
    private function addDriverLicences(&$choices)
    {
        $choices += [
            'licences.type.driver' => [
                Licence::TYPE_DRIVER_M => 'licences.type.driver_licence.m',
                Licence::TYPE_DRIVER_E => 'licences.type.driver_licence.e',
                Licence::TYPE_DRIVER_JRE => 'licences.type.driver_licence.jre',
                Licence::TYPE_DRIVER_JRD => 'licences.type.driver_licence.jrd',
                Licence::TYPE_DRIVER_D => 'licences.type.driver_licence.d',
                Licence::TYPE_DRIVER_D2 => 'licences.type.driver_licence.d2',
                Licence::TYPE_DRIVER_B => 'licences.type.driver_licence.b',
                Licence::TYPE_DRIVER_C => 'licences.type.driver_licence.c',
                Licence::TYPE_DRIVER_R => 'licences.type.driver_licence.r',
            ],
        ];
    }

    /**
     * @param $choices
     */
    private function addOrganisatorChoices(&$choices)
    {
        $choices += [
            Licence::TYPE_TRACK => 'licences.type.track_licence',
        ];
    }

    /**
     * @param $choices
     */
    private function addDeclarantChoices(&$choices)
    {
        $choices += [
            Licence::TYPE_DECLARANT_A => 'licences.type.declarant_licence.a',
            Licence::TYPE_DECLARANT_B => 'licences.type.declarant_licence.b',
            Licence::TYPE_DECLARANT_K => 'licences.type.declarant_licence.k',
        ];
    }

    /**
     * @param $choices
     */
    private function addJudgeChoices(&$choices)
    {
        $choices += [
            'licences.type.judge' => [
                Licence::TYPE_JUDGE_FIRST => 'licences.type.judge_licence.first',
                Licence::TYPE_JUDGE_SECOND => 'licences.type.judge_licence.second',
                Licence::TYPE_JUDGE_THIRD => 'licences.type.judge_licence.third',
                Licence::TYPE_JUDGE_NATIONAL => 'licences.type.judge_licence.national',
                Licence::TYPE_JUDGE_INTERNATIONAL => 'licences.type.judge_licence.international',
                Licence::TYPE_JUDGE_TRAINEE => 'licences.type.judge_licence.trainee',
            ],
        ];
    }

    /**
     * @param $choices
     */
    private function addLegalLicences(&$choices)
    {
        $choices += [
            Licence::TYPE_MEMBERSHIP => 'licences.type.membership',
        ];
    }
}
