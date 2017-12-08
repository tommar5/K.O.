<?php namespace AppBundle\EventListener;

use AppBundle\Entity\FileUpload;
use AppBundle\Event\DocumentStatusChangeEvent;
use AppBundle\EventListener\Traits\RecipientTrait;
use AppBundle\Mailer\Mailer;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class class ApplicationStatusChangeListener
 * @package AppBundle\EventListener
 */
class DocumentStatusChangeListener
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

    public function notify(DocumentStatusChangeEvent $event)
    {
        $application = $event->getApplication();
        if ($event->getCompetition()) {
            $application = $event->getCompetition();
        }
        switch ($event->getStatus()) {
            case FileUpload::STATUS_ADDED_ADDITIONAL_COMPETITION_RULES:
                $this->mailer->users($this->getUsersByRoles([User::$roleMap['ROLE_LASF_COMMITTEE'], User::$roleMap['ROLE_SECRETARY'], User::$roleMap['ROLE_ADMIN']], $application->getSport()),
                    'inform_about_added_additional_competition_rules', [
                        'application' => $event->getApplication()->getName(),
                        'customer' => $event->getUser()->getMemberName(),
                    ]);
                break;
            case FileUpload::STATUS_ADDED_SAFETY_PLAN:
                $this->mailer->users($this->getUsersByRoles([User::$roleMap['ROLE_SVO_COMMITTEE']]),
                    'inform_about_added_safety_plan', [
                        'application' => $event->getApplication()->getName(),
                        'customer' => $event->getUser()->getMemberName(),
                    ]);
                break;
            case FileUpload::STATUS_COMMENTED_ADDITIONAL_COMPETITION_RULES:
                $users = array_merge(
                    $this->getUsersByRoles([
                            User::$roleMap['ROLE_LASF_COMMITTEE'],
                            User::$roleMap['ROLE_SECRETARY'],
                            User::$roleMap['ROLE_ADMIN'],
                            User::$roleMap['ROLE_JUDGE_COMMITTEE']
                        ]
                    ),
                    [$event->getUser()]
                );
                $this->mailer->users($users,
                    'inform_about_' . $event->getStatus(), [
                        'application' => $event->getApplication()->getName(),
                        'comment' => $event->getComment(),
                    ]);
                break;
            case FileUpload::STATUS_COMMENTED_SAFETY_PLAN:
                $users = array_merge(
                    $this->getUsersByRoles([User::$roleMap['ROLE_SVO_COMMITTEE'], User::$roleMap['ROLE_ADMIN']]),
                    [$event->getUser()]
                );
                $this->mailer->users($users,
                    'inform_about_' . $event->getStatus(), [
                        'application' => $event->getApplication()->getName(),
                        'comment' => $event->getComment(),
                    ]);
                break;
            case FileUpload::STATUS_ADDED_COMPETITION_INSURANCE:
            case FileUpload::STATUS_ADDED_OTHER_DOCUMENTS:
                $this->mailer->users($this->getUsersByRoles([User::$roleMap['ROLE_SECRETARY'], User::$roleMap['ROLE_ADMIN']]),
                    'inform_' . $event->getStatus(), [
                        'application' => $application->getName(),
                    ]);
                break;
            case FileUpload::STATUS_ADDED_TRACK_ACCEPTANCE:
                $this->mailer->users($this->getUsersByRoles([User::$roleMap['ROLE_SECRETARY'], User::$roleMap['ROLE_SVO_COMMITTEE']]),
                    'inform_added_track_acceptance', [
                        'application' => $event->getApplication()->getName(),
                    ]);
                break;
            case FileUpload::STATUS_ADDED_ORGANISATOR_LICENCE:
                $this->mailer->user($event->getUser(), 'inform_about_added_organisator_licence', [
                    'application' => $application->getName(),
                ]);
                break;
            case FileUpload::STATUS_CONFIRMED_SAFETY_PLAN:
            case FileUpload::STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES:
            case FileUpload::STATUS_CONFIRMED_TRACK_ACCEPTANCE:
            case FileUpload::STATUS_ADDED_TRACK_LICENCE:
            case FileUpload::STATUS_CONFIRMED_TRACK_LICENCE:
            case FileUpload::STATUS_CONFIRMED_ORGANISATOR_LICENCE:
                $this->mailer->user($event->getUser(), 'inform_about_' . $event->getStatus(), [
                    'application' => $application->getName(),
                ]);
                break;
            case FileUpload::TYPE_COMPETITION_RESULTS:
            case FileUpload::TYPE_COMPETITION_REPORT:
            case FileUpload::TYPE_COMPETITION_BULLETIN:
            case FileUpload::TYPE_COMPETITION_SKK_REPORT:
                $this->mailer->users($this->getUsersByRoles([User::$roleMap['ROLE_ADMIN']]),
                    'inform_added_competition_' . $event->getStatus(), [
                        'application' => $event->getApplication()->getName(),
                        'customer' => $event->getUser()->getMemberName(),
                    ]);
                break;
        }
    }

}
