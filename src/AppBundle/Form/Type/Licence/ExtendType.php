<?php namespace AppBundle\Form\Type\Licence;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use AppBundle\EventListener\Licence\InsuranceFieldListener;
use AppBundle\EventListener\Licence\LicencePreSubmitSubscriber;
use AppBundle\Form\DocumentType;
use AppBundle\Lasf\UrgencyChoices;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;

class ExtendType extends AbstractType
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
     * @var UrgencyChoices
     */
    private $urgencyChoices;

    /**
     * @param User $user
     * @param Licence $licence
     * @param UrgencyChoices $urgencyChoices
     */
    public function __construct(User $user, Licence $licence, UrgencyChoices $urgencyChoices)
    {
        $this->user = $user;
        $this->licence = $licence;
        $this->urgencyChoices = $urgencyChoices;

        if ($licence->isDriverLicence()) {
            $licence->addDocument(new FileUpload(FileUpload::TYPE_MED_CERT));
            $licence->addDocument(new FileUpload(FileUpload::TYPE_INSURANCE));
            $licence->addDocument(new FileUpload(FileUpload::TYPE_DRIVERS_LICENCE));
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'licence_info';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lpExpressList = $options['allow_extra_fields'];
        $licence = $this->licence;
        $builder->add('documents', 'collection', [
            'by_reference' => false,
            'allow_delete' => true,
            'delete_empty' => true,
            'type' => new DocumentType($this->user, true, $licence),
            'label' => false,
        ]);

        if ($licence->isJudgeLicence() || $licence->isDriverLicence()) {
            $builder->add('sports', 'entity', [
                'class' => 'AppBundle:Sport',
                'label' => 'licences.label.sports',
                'required' => true,
                'placeholder' => 'licences.sport_select.placeholder',
                'choice_label' => 'name',
                'multiple' => true,
            ]);
        }

        if ($licence->getDocumentsByType(FileUpload::TYPE_INSURANCE)->count()) {
            $builder->add('identityCode', 'text', [
                'label' => 'licences.label.insurance.code',
                'required' => false,
            ])
                ->add('lasfInsurance', 'checkbox', [
                    'label' => 'licences.label.insurance.agree',
                    'label_attr' => ['class' => 'control-label bold-text'],
                    'required' => false
                ]);
        }

        if ($licence->isJudgeLicence() || $licence->isDriverLicence()) {
            $builder->add('deliverTo', 'hidden', [
                'constraints' => [new NotBlank()],
            ])
                ->add('deliverTo', 'choice', [
                    'label' => false,
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
                ]);
        }

        if ($this->urgencyChoices) {
            $builder->add('urgency', 'choice', [
                'label' => 'licences.label.urgency',
                'choices' => [
                    Licence::URGENCY_STANDARD => $this->urgencyChoices->getChoiceTitle(Licence::URGENCY_STANDARD),
                    Licence::URGENCY_URGENT => $this->urgencyChoices->getChoiceTitle(Licence::URGENCY_URGENT),
                ],
                'choices_as_values' => false,
                'expanded' => true,
                'constraints' => [
                    new NotNull(),
                ],
                'required' => true,
            ]);
        }

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
}
