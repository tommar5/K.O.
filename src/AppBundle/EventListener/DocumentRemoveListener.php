<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\Application;
use AppBundle\Entity\FileUpload;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class DocumentRemoveListener
{
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof FileUpload) {
            $applications = $entity->getApplications();
            foreach ($applications as $application) {
                if (!$application->isPaid()) {
                    switch ($entity->getType()) {
                        case Application::STATUS_CONTRACT_UPLOADED_BY_LASF:
                            $application->setStatus(Application::STATUS_CONTRACT_BY_LASF_DELETED);
                            break;
                        case Application::STATUS_CONTRACT_UPLOADED_BY_ORGANISATOR:
                            $application->setStatus(Application::STATUS_CONTRACT_BY_ORGANISATOR_DELETED);
                            break;
                    }
                }
            }
        }
    }
}