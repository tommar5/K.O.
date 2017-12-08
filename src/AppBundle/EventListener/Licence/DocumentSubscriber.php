<?php
namespace AppBundle\EventListener\Licence;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DocumentSubscriber implements EventSubscriberInterface
{
    /**
     * @var User
     */
    private $user;

    /**
     * DocumentSubscriber constructor.
     * @param User|null $user
     */
    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        if ($this->user && $event->getData()->getStatus() == FileUpload::STATUS_NEW) {
            $type = $event->getData()->getType();
            $date = new \DateTime('now');
            if ($type == FileUpload::TYPE_INSURANCE) {
                $date = new \DateTime('last day of december');
            }
            foreach ($this->user->getLicences() as $licence) {
                foreach ($licence->getDocuments() as $document) {
                    if ($this->validDocument($document, $event->getData()->getType()) &&
                        $this->validDate($document, $date)
                    ) {
                        $event->getForm()->add('useOldFile', 'checkbox', [
                            'label' => 'file_uploads.label.use_old',
                            'label_attr' => [
                                'class' => 'control-label bold-text',
                            ],
                            'required' => false,
                            'mapped' => false,
                        ]);

                        return;
                    }
                }
            }
        }
    }

    /**
     * @param FileUpload $document
     * @param $type
     * @return bool
     */
    private function validDocument(FileUpload $document, $type)
    {
        return $document->getId() && $document->isType($type) && ($document->isNew() || $document->isApproved());
    }

    /**
     * @param FileUpload $document
     * @param \DateTime $date
     * @return bool
     */
    private function validDate(FileUpload $document, \DateTime $date)
    {
        return !$document->haveDateField() ||
            ($document->haveDateField() &&
                $document->getValidUntil() &&
                $document->getValidUntil()->format('Y-m-d') >= $date->format('Y-m-d')
            );
    }
}
