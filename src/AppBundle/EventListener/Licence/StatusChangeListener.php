<?php namespace AppBundle\EventListener\Licence;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\Licence;
use AppBundle\Event\StatusChangeEvent;
use AppBundle\Mailer\Mailer;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class StatusChangeListener
 * @package AppBundle\EventListener
 */
class StatusChangeListener
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Router
     */
    private $router;
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * StatusChangeListener constructor.
     * @param Mailer|\Swift_Mailer $mailer
     * @param Router $router
     * @param EntityManager $em
     */
    public function __construct(Mailer $mailer, Router $router, EntityManager $em)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->em = $em;
    }

    private function getAdmins()
    {
        return $this->em->getRepository(User::class)->createQueryBuilder('u')
            ->where('BIT_AND(u.roles, :role) > 0')
            ->setParameter('role', User::$roleMap['ROLE_ADMIN'])
            ->getQuery()
            ->getResult();
    }

    public function notify(StatusChangeEvent $event)
    {
        switch ($event->getStatus()) {
            case Licence::STATUS_UPLOADED:
                $this->mailer->users($this->getAdmins(), 'inform_about_licence', [
                    'customer' => $event->getUser(),
                    'licence' => $event->getLicenceType(),
                ]);
                break;
            case Licence::STATUS_WAITING_EDIT:
                if ($event->getFileStatus() == FileUpload::STATUS_REJECTED) {
                    $this->mailer->user($event->getUser(), 'inform_rejected_file', [
                        'licence' => $event->getLicenceType(),
                        'file' => $event->getFileType(),
                    ]);
                } else {
                    foreach ($this->getAdmins() as $admin) {
                        $this->mailer->user($admin, 'inform_rejected_file_update', [
                            'customer' => $event->getUser(),
                            'licence' => $event->getLicenceType(),
                            'file' => $event->getFileType(),
                        ]);
                    }
                }
                break;
            case Licence::STATUS_WAITING_CONFIRM:
                $this->mailer->users($this->getAdmins(), 'inform_rejected_file_update', [
                    'customer' => $event->getUser(),
                    'licence' => $event->getLicenceType(),
                    'file' => $event->getFileType(),
                ]);
                break;
            case Licence::STATUS_EXTEND:
                $this->mailer->users($this->getAdmins(), 'extend_licence_request', [
                    'licence' => $event->getLicenceType(),
                ]);
                break;
            case Licence::STATUS_PAID:
                $this->mailer->users($this->getAdmins(), 'inform_paid_licence', [
                    'licence' => $event->getLicence(),
                ]);
                break;
            case Licence::STATUS_PRODUCED:
                $this->mailer->user($event->getUser(), 'inform_produced_licence', [
                    'licence' => $event->getLicenceType(),
                ]);
                break;
        }
    }
}
