<?php

namespace AppBundle\Lasf;

use AppBundle\Entity\Licence;
use AppBundle\Entity\FileUpload;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;

class LicenceDocumentHandler
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * LicenceDocumentHandler constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Licence $licence
     * @param Form $form
     */
    public function handleLicenceDocuments(Licence $licence, Form $form)
    {
        if ($form->has('documents')) {
            foreach ($form->get('documents') as $document) {
                if ($document->get('file')->getData()) {
                    foreach ($document->get('file')->getData() as $file) {
                        if ($file) {
                            $newDocument = clone $document->getData();
                            $newDocument->setFile($file);
                            $newDocument->setStatus(FileUpload::STATUS_NEW);
                            $licence->addDocument($newDocument);
                            $licence->getUser()->addDocument($newDocument);
                            $this->em->persist($newDocument);
                        }
                    }
                }
            }
            $this->addOldDocuments($licence, $form);
        }
    }

    /**
     * @param Licence $licence
     * @param Form $form
     */
    public function addOldDocuments(Licence $licence, Form $form)
    {
        /** @var LicenceRepository $LicenceRepository */
        $LicenceRepository = $this->em->getRepository(Licence::class);
        foreach ($form->get('documents') as $document) {
            if ($document->has('useOldFile') && $document->getIterator()->offsetGet('useOldFile')->getData()) {
                $type = $document->getData()->getType();
                /** @var Licence $oldLicence */
                $oldLicence = $LicenceRepository->getReusableLicence($licence->getUser(), $type);
                $oldDocuments = $oldLicence->getDocumentsByType($type);
                $date = $this->getValidUntil($document->getData());
                if (!$oldDocuments->isEmpty()) {
                    /** @var FileUpload $oldDocument */
                    foreach ($oldDocuments as $oldDocument) {
                        if (!$date || ($date && $oldDocument->getValidUntil() >= $date)) {
                            /** @var FileUpload $newDocument */
                            $newDocument = clone $oldDocument;
                            $newDocument->setStatus(FileUpload::STATUS_NEW);
                            $licence->addDocument($newDocument);
                            $licence->getUser()->addDocument($newDocument);
                            $this->em->persist($newDocument);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param FileUpload $document
     * @return \DateTime|null
     */
    private function getValidUntil(FileUpload $document)
    {
        $date = null;
        if ($document->haveDateField()) {
            $date = new \DateTime('now 00:00:00');
            if ($document->isValidThisYear()) {
                $date = new \DateTime('last day of december 00:00:00');
            }
        }

        return $date;
    }
}
