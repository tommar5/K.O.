<?php

namespace AppBundle\Lasf;

use AppBundle\Entity\FileUpload;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;
use Vich\UploaderBundle\Storage\FileSystemStorage;

class DocumentHandler
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var FileSystemStorage
     */
    private $vichFileSystemStorage;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * DocumentHandler constructor.
     * @param EntityManager $em
     * @param FileSystemStorage $vichFileSystemStorage
     * @param Filesystem $filesystem
     */
    public function __construct(EntityManager $em, FileSystemStorage $vichFileSystemStorage, Filesystem $filesystem)
    {
        $this->em = $em;
        $this->vichFileSystemStorage = $vichFileSystemStorage;
        $this->filesystem = $filesystem;
    }

    /**
     * @param FileUpload $oldFile
     */
    public function removeDocument(FileUpload $oldFile)
    {
        /** @var FileUploadRepository $fileUploadRepository */
        $fileUploadRepository = $this->em->getRepository(FileUpload::class);
        if (!$fileUploadRepository->fileExist($oldFile->getFileName())) {
            $path = $this->vichFileSystemStorage->resolvePath($oldFile, 'file');
            $this->filesystem->remove($path);
        }
    }
}
