<?php
namespace AppBundle\EventListener\Licence;

use AppBundle\Entity\FileUpload;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\File;

class InsuranceFieldListener implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'onPreSubmit'
        );
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        /** @var Form $form */
        $form = $event->getForm();
        /** @var Licence $licence */
        $licence = $form->getData();

        if (($licence->isJudgeLicence() || $licence->isDriverLicence()) && isset($data['lasfInsurance'])) {
            $form->add('identityCode', 'text', [
                'label' => 'licences.label.insurance.code',
                'required' => true,
            ]);

            $documents = $form->get('documents')->getIterator();
            foreach ($documents as $document) {
                if ($document->getData()->isType(FileUpload::TYPE_INSURANCE)) {
                    $validUntilParams = [
                        'label' => 'file_uploads.label.valid_until',
                        'attr' => ['class' => 'date', 'data-type' => $document->getData()->getType()],
                        'widget' => 'single_text',
                        'format' => 'yyyy-MM-dd',
                    ];

                    $document
                        ->add('file', 'file', [
                            'constraints' => [
                                new File([
                                    'mimeTypes' => FileUpload::TYPE_IMAGE_MIME,
                                    'maxSize' => '3M',
                                ]),
                            ],
                            'label' => 'file_uploads.label.file',
                            'required' => false,
                        ])
                        ->add('validUntil', 'datetime', $validUntilParams + ['required' => false]);
                }
            }
        }
    }
}