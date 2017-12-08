<?php namespace AppBundle\Form;

use AppBundle\Entity\Licence;
use AppBundle\Form\Type\Licence\TypeSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\User;
use AppBundle\Entity\FileUpload;

class LicenceType extends AbstractType
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Licence
     */
    private $licence;

    public function __construct(User $user, Licence $licence)
    {
        $this->user = $user;
        $this->licence = $licence;

        if ($licence->isMembershipLicence()) {
            if ($user->documentTypeCount(FileUpload::TYPE_RECOMMENDATION, $licence) == 0) {
                $licence->addDocument(new FileUpload(FileUpload::TYPE_RECOMMENDATION));
                $licence->addDocument(new FileUpload(FileUpload::TYPE_RECOMMENDATION));
            } elseif ($user->documentTypeCount(FileUpload::TYPE_RECOMMENDATION, $licence) == 1) {
                $licence->addDocument(new FileUpload(FileUpload::TYPE_RECOMMENDATION));
            }
            if (!$user->documentTypeCount(FileUpload::TYPE_REGISTRY, $licence)) {
                $licence->addDocument(new FileUpload(FileUpload::TYPE_REGISTRY));
            }
            if (!$user->documentTypeCount(FileUpload::TYPE_ACTIVITY_DESC, $licence)) {
                $licence->addDocument(new FileUpload(FileUpload::TYPE_ACTIVITY_DESC));
            }
            if (!$user->documentTypeCount(FileUpload::TYPE_STATUTE_COPY, $licence)) {
                $licence->addDocument(new FileUpload(FileUpload::TYPE_STATUTE_COPY));
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
        return 'licence';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $licence = $this->licence;
        $builder->add('expiresAt', 'datetime', [
            'label' => 'licences.label.expires_at',
            'attr' => ['class' => 'date'],
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
        ]);
        $builder->add('serialNumber', 'integer', [
            'label' => 'licences.label.serial_number',
            'required' => false
        ]);
        $builder->add('comment', 'text', [
            'label' => 'licences.label.comment',
            'required' => false
        ]);

        if ($licence->isMembershipLicence()) {
            $builder->add('personalCode', 'choice', [ // reuse field, is associated
                'label' => 'user.label.associated',
                'placeholder' => 'licences.edit.choose_membership_type',
                'choices' => [
                    'user.edit.not_accociated' => 0,
                    'user.edit.accociated' => 1,
                ],
                'data' => $licence->isMembershipLicence() ? (int)$licence->getPersonalCode() : null,
                'choices_as_values' => true,
                'required' => false,
            ]);

            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($licence) {
                /** @var Licence $data */
                $data = $event->getData();

                if ($licence->isMembershipLicence() && $data['personalCode'] == 1) {
                    unset($data['documents']);
                    $event->getForm()->remove('documents');
                }
            });
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Licence $data */
            $data = $event->getData();

            $types = (new TypeSelectType($data->getUser()))->getAvailableTypesForChange($data);

            if (!$data->isProduced() && sizeof($types) > 0) {
                $event->getForm()->add('type', 'choice', [
                    'label' => 'licences.label.type',
                    'choices' => $types,
                ]);
            }
        });

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
