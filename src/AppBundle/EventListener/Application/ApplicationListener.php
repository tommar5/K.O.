<?php namespace AppBundle\EventListener\Application;

use AppBundle\Entity\Steward;
use AppBundle\Entity\SubCompetition;
use AppBundle\EventListener\Traits\RecipientTrait;
use Doctrine\ORM\Event\OnFlushEventArgs;
use AppBundle\Entity\Application;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class class ApplicationListener
 * @package AppBundle\EventListener
 */
class ApplicationListener
{
    use RecipientTrait;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * ApplicationListener constructor.
     * @param ContainerInterface $service
     */
    public function __construct(ContainerInterface $service)
    {
        $this->serviceContainer = $service;
    }

    /**
     * @param $application
     * @return string
     */
    private function getStewards($application)
    {
        $stewards = $application->getStewards();
        $result = [];
        foreach ($stewards as $steward) {
            $result[] = $steward->getUser()->getFullName();
        }

        return implode(", ", $result);
    }

    /**
     * @param $application
     * @return string
     */
    private function getJudges($application)
    {
        $judges = $application->getJudges();
        $result = [];
        foreach ($judges as $judge) {
            $result[] = $judge->getFullName();
        }

        return implode(", ", $result);
    }

    public function onFlush(OnFlushEventArgs $event)
    {
        $this->em = $event->getEntityManager();
        $this->mailer = $this->serviceContainer->get('mail');
        $uow = $this->em->getUnitOfWork();
        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );

        foreach ($entities as $entity) {
            if (!($entity instanceof Application || $entity instanceof SubCompetition)) {
                continue;
            }

            $changes = $uow->getEntityChangeSet($entity);
            foreach ($changes as $property => $change) {
                if ($change[1]) {
                    $user = $entity instanceof SubCompetition ? $entity->getApplication()->getUser() : $entity->getUser();
                    switch ($property) {
                        case "observer":
                            $this->mailer->user($user, 'inform_about_assigned_observer', [
                                'observer' => $change[1],
                                'application' => $entity->getName(),
                            ]);
                            break;
                        case "skkHead":
                            $this->mailer->user($user, 'inform_about_assigned_skkhead', [
                                'skkHead' => $change[1],
                                'application' => $entity->getName(),
                            ]);
                            $this->mailer->users($this->getUsersByRoles([User::$roleMap['ROLE_SECRETARY']]),
                                'inform_about_assigned_skkhead', [
                                'skkHead' => $change[1],
                                'application' => $entity->getName(),
                            ]);
                            break;
                    }
                }
            }
        }

        $collections = array_merge(
            $uow->getScheduledCollectionUpdates(),
            $uow->getScheduledCollectionDeletions()
        );

        foreach ($collections as $collection) {
            if ($collection->getOwner() instanceof Application || $collection->getOwner() instanceof SubCompetition) {

                $changes = array_merge($collection->getInsertDiff(), $collection->getDeleteDiff());

                if ($changes && isset($changes[0]) && $changes[0] instanceof Steward) {
                    $user = $entity instanceof SubCompetition ? $collection->getOwner()->getApplication()->getUser() : $collection->getOwner()->getUser();
                    $this->mailer->user($user, 'inform_about_assigned_steward', [
                        'steward' => $this->getStewards($collection->getOwner()),
                        'application' => $collection->getOwner()->getName(),
                    ]);
                } elseif ($changes && isset($changes[0]) && $changes[0] instanceof User) {
                    $this->mailer->users($this->getUsersByRoles([User::$roleMap['ROLE_ADMIN']]),
                        'inform_about_assigned_judges', [
                        'judges' => $this->getJudges($collection->getOwner()),
                        'application' => $collection->getOwner()->getName(),
                    ]);
                }
            }
        }
    }
}
