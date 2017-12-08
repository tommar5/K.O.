<?php namespace AppBundle\EventListener\Application;

use AppBundle\Entity\Application;
use AppBundle\Event\ApplicationStatusChangeEvent;
use AppBundle\EventListener\Traits\RecipientTrait;
use AppBundle\Mailer\Mailer;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class class ApplicationStatusChangeListener
 * @package AppBundle\EventListener
 */
class ApplicationStatusChangeListener
{
    use RecipientTrait;

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

    public function notify(ApplicationStatusChangeEvent $event)
    {
        switch ($event->getStatus()) {
            case Application::STATUS_UNCONFIRMED:
                $customer = $event->getUser()->getMemberName() ? $event->getUser()->getMemberName() : $event->getUser();
                $this->mailer->users($this->getUsersByRoles([ User::$roleMap['ROLE_SECRETARY'], User::$roleMap['ROLE_LASF_COMMITTEE']], $event->getApplication()->getSport()),
                    'inform_about_application', [
                        'application' => $event->getApplication()->getName(),
                        'customer' => $customer,
                    ]);
                break;
            case Application::STATUS_CONFIRMED:
                $this->mailer->user($event->getUser(), 'inform_about_confirmed_application', [
                    'application' => $event->getApplication()->getName(),
                ]);
                break;
            case Application::STATUS_DECLINED:
                $this->mailer->user($event->getUser(), 'inform_about_declined_application', [
                    'application' => $event->getApplication()->getName(),
                    'reason' => $event->getApplication()->getReason(),
                ]);
                break;
            case Application::STATUS_CONTRACT_UPLOADED_BY_LASF:
                $this->mailer->user($event->getUser(), 'inform_about_uploaded_contract_by_lasf', [
                    'application' => $event->getApplication()->getName(),
                ]);
                break;
            case Application::STATUS_CONTRACT_UPLOADED_BY_ORGANISATOR:
                $this->mailer->users($this->getUsersByRoles([ User::$roleMap['ROLE_SECRETARY'], User::$roleMap['ROLE_ADMIN']]),
                    'inform_about_uploaded_contract_by_organisator', [
                        'application' => $event->getApplication()->getName(),
                        'customer' => $event->getUser(),
                    ]);
                break;
            case Application::STATUS_NOT_PAID:
                $invoice = '';
                foreach ($event->getApplication()->getDocuments() as $invoiceFile) {
                    if ($invoiceFile->getType() == 'invoice') {
                        $invoice = $invoiceFile->getFileName();
                    }
                }
                $this->mailer->user($event->getUser(), 'inform_about_invoice_application', [
                    'application' => $event->getApplication()->getName(),
                    'invoice' => $invoice,
                ]);
                break;
            case Application::STATUS_PAID:
                $users = array_merge(
                    $this->getUsersByRoles([User::$roleMap['ROLE_SECRETARY']]),
                    [$event->getUser()]
                );
                $this->mailer->users($users,
                    'inform_paid_application', [
                        'application' => $event->getApplication()->getName(),
                    ]);
                break;
        }
    }

}
