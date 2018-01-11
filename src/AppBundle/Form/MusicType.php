<?php
namespace AppBundle\Form;

use AppBundle\Entity\Music;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MusicType extends AbstractType
{
    public function getName()
    {
        return 'music_type';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $type = 'mp3';

            $event->getForm()->add('artist', 'text', [
                'label' => 'sport.label.sport',
            ])
                ->add('name', 'text', [
                    'label' => 'sport.label.sport',
                ])
                ->add('album', 'text', [
                    'label' => 'sport.label.sport'
                ])
                ->add('musicFile', 'file', [
                    'label' => 'file_uploads.label.file',
                    'required' => true,
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
            'data_class' => Music::class
        ]);
    }

    /**
     * @param $type
     * @return array
     */
    private function getConstraints($type)
    {
        $constrains = [
            new File([
                'mimeTypes' => $type,
                'maxSize' => '1M',
            ]),
            new NotBlank(),
        ];
        return $constrains;
    }

    /**
     * @param $type
     * @return array
     */
    private function getAttributes($type)
    {
        $acceptedFiles = 'mp3';

        $attr = [
            'class' => 'btn-file',
            'accept' => $acceptedFiles,
        ];

        return $attr;
    }
}
