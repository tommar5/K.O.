<?php
namespace AppBundle\EventListener\Licence;

use AppBundle\Entity\FileUpload;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\File;

class LicencePreSubmitSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
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

        if ($form->has('documents')) {
            $documents = $form->get('documents');

            $documents = $documents->getIterator();
            /** @var \Symfony\Component\Form\Form $document */
            foreach ($documents as $key => $document) {
                if (isset($data['documents'][$key]['useOldFile']) && $data['documents'][$key]['useOldFile'] == 1) {
                    $type = $document->getData()->getType();
                    $document
                        ->add('file', 'file', [
                            'constraints' => $this->getConstraints($type),
                            'label' => 'file_uploads.label.file',
                            'required' => false,
                        ]);

                    if ($type == FileUpload::TYPE_DRIVERS_LICENCE) {
                        $document->add('number', 'text', [
                            'label' => 'file_uploads.label.number',
                            'required' => false,
                        ]);
                    }

                    if ($document->has('validUntil')) {
                        $validUntilParams = [
                            'label' => 'file_uploads.label.valid_until',
                            'attr' => [
                                'class' => 'date',
                                'data-type' => $document->getData()->getType(),
                            ],
                            'widget' => 'single_text',
                            'format' => 'yyyy-MM-dd',
                            'required' => false,
                        ];
                        $document->add('validUntil', 'datetime', $validUntilParams);
                    }
                }
            }
        }
    }


    /**
     * @param $type
     * @return array
     */
    private function getConstraints($type)
    {
        $mimeTypes = $type == FileUpload::TYPE_PHOTO ?
            FileUpload::TYPE_IMAGE_MIME :
            FileUpload::TYPE_FILE_MIME;

        return [
            new File(
                [
                    'mimeTypes' => $mimeTypes,
                    'maxSize' => '3M',
                ]
            ),
        ];
    }

}