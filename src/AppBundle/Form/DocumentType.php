<?php namespace AppBundle\Form;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use AppBundle\EventListener\Licence\DocumentSubscriber;
use AppBundle\Validator\Constraints\DoesNotExpireThisYear;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class DocumentType extends AbstractType
{
    const JUNIOR_LICENCE_TYPES = [
        Licence::TYPE_DRIVER_JRE,
        Licence::TYPE_DRIVER_JRD,
    ];

    /**
     * @var User
     */
    private $user;

    /**
     * @var bool
     */
    private $multiple;

    /**
     * @var bool
     */
    private $canUseOld;

    /**
     * @var Licence
     */
    private $licence;

    /**
     * DocumentType constructor.
     * @param User|null $user
     * @param bool $multiple
     * @param bool $canUseOld
     * @param Licence $licence
     */
    public function __construct(User $user = null, $multiple = false, Licence $licence = null, $canUseOld = true)
    {
        $this->user = $user;
        $this->multiple = $multiple;
        $this->canUseOld = $canUseOld;
        $this->licence = $licence;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'document';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->canUseOld) {
            $builder->addEventSubscriber(new DocumentSubscriber($this->user));
        }
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var FileUpload $data */
            $data = $event->getData();
            $type = $data->getType();

            if ($this->multiple) {
                $required = false;
                $multiple = true;
                $mapped = false;
            } else {
                $required = true;
                $multiple = false;
                $mapped = true;
            }

            if ($this->fileIsNotRequired($type)) {
                $event->getForm()->add('file', 'file', [
                    'label' => 'file_uploads.label.file',
                    'required' => false,
                    'multiple' => $multiple,
                    'mapped' => $mapped,
                    'attr' => $this->getAttributes($type),
                ]);
            } else {
                $event->getForm()->add('file', 'file', [
                    'label' => 'file_uploads.label.file',
                    'multiple' => $multiple,
                    'mapped' => $mapped,
                    'required' => $required,
                    'attr' => $this->getAttributes($type),
                    'validation_groups' => [
                        "Default",
                        "documents",
                    ],
                    'constraints' => $this->getConstraints($type),
                    'label_attr' => [
                        'class' => 'required',
                    ],
                ]);
            }

            if ($type == FileUpload::TYPE_DRIVERS_LICENCE) {
                $event->getForm()->add('number', 'text', $this->getOptions($this->licence->getType()));
            }

            if (!$this->datePresent($type)) {
                return;
            }

            $validUntilParams = [
                'label' => 'file_uploads.label.valid_until',
                'attr' => [
                    'class' => 'date',
                    'data-type' => $type,
                ],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ];

            if ($this->documentDateIsRequired($type)) {
                $dateValidators = [
                    new NotBlank(),
                ];

                if ($type == FileUpload::TYPE_INSURANCE) {
                    $dateValidators[] = new DoesNotExpireThisYear();
                }

                $event->getForm()->add('validUntil', 'datetime', $validUntilParams + [
                        'required' => true,
                        'constraints' => $dateValidators,
                        'validation_groups' => [
                            "Default",
                            "documents",
                        ],
                    ]);
            } else {
                $event->getForm()->add('validUntil', 'datetime', $validUntilParams + ['required' => false,]);
            }
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FileUpload::class,
        ]);
    }

    /**
     * @param $type
     * @return bool
     */
    private function datePresent($type)
    {
        switch ($type) {
            case FileUpload::TYPE_PHOTO:
            case FileUpload::TYPE_PARENT_AGREEMENT:
            case FileUpload::TYPE_PREVIOUS_LICENCE:
            case FileUpload::TYPE_ACCEPTANCE_ACT:
            case FileUpload::TYPE_OTHER:
            case FileUpload::TYPE_SAFETY_PLAN:
            case FileUpload::TYPE_COMP_RESULT:
            case FileUpload::TYPE_JUDGE_CERT:
            case FileUpload::TYPE_RECOMMENDATION:
            case FileUpload::TYPE_REGISTRY:
            case FileUpload::TYPE_ACTIVITY_DESC:
            case FileUpload::TYPE_STATUTE_COPY:
            case FileUpload::TYPE_RALLY_PARTICIPANT:
                return false;
        }

        return true;
    }

    /**
     * @param $type
     * @return bool
     */
    private function documentDateIsRequired($type)
    {
        switch ($type) {
            case FileUpload::TYPE_MED_CERT:
            case FileUpload::TYPE_INSURANCE:
                return true;
                break;
            case FileUpload::TYPE_DRIVERS_LICENCE:
                if (in_array($this->licence->getType(), self::JUNIOR_LICENCE_TYPES)) {
                    return false;
                }
                return true;
        }

        return false;
    }

    /**
     * @param $type
     * @return bool
     */
    private function fileIsNotRequired($type)
    {
        switch ($type) {
            case FileUpload::TYPE_RECOMMENDATION:
            case FileUpload::TYPE_REGISTRY:
            case FileUpload::TYPE_ACTIVITY_DESC:
            case FileUpload::TYPE_STATUTE_COPY:
                if ($this->user && $this->user->hasAskedForMembership()) {
                    return true;
                }
                break;
        }

        switch ($type) {
            case FileUpload::TYPE_PREVIOUS_LICENCE:
            case FileUpload::TYPE_OTHER:
            case FileUpload::TYPE_COMP_RESULT:
            case FileUpload::TYPE_JUDGE_CERT:
            case FileUpload::TYPE_EXAM:
            case FileUpload::TYPE_SCHOOL_CERT:
            case FileUpload::TYPE_RALLY_PARTICIPANT:
                return true;
                break;
            case FileUpload::TYPE_DRIVERS_LICENCE:
                if (in_array($this->licence->getType(), self::JUNIOR_LICENCE_TYPES)) {
                    return true;
                }
        }

        return false;
    }

    /**
     * @param $type
     * @return array
     */
    private function getOptions($type)
    {
        $options = [
            'label' => 'file_uploads.label.number',
            'validation_groups' => [
                "Default",
                "documents",
            ],
        ];

        if (in_array($type, self::JUNIOR_LICENCE_TYPES)) {
            $options += [
                'required' => false,
            ];
        } else {
            $options += [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ];
        }

        return $options;
    }

    /**
     * @param $type
     * @return array
     */
    private function getConstraints($type)
    {
        if ($this->multiple) {
            $constrains = [
                new All([
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]),
            ];
        } else {
            $mimeTypes = $type == FileUpload::TYPE_PHOTO ?
                ['image/*',] :
                ['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',];

            $constrains = [
                    new File([
                        'mimeTypes' => $mimeTypes,
                        'maxSize' => '3M',
                    ]),
                    new NotBlank(),
                ];
        }

        return $constrains;
    }

    /**
     * @param $type
     * @return array
     */
    private function getAttributes($type)
    {
        $attr = [];
        if ($this->multiple) {
            $acceptedFiles = $type == FileUpload::TYPE_PHOTO ?
                'gif|jpg|jpeg|png|tif' :
                'gif|jpg|jpeg|tif|pdf|png|doc|docx';
            $attr = [
                'class' => 'btn-file',
                'accept' => $acceptedFiles,
            ];
        }

        return $attr;
    }

}
