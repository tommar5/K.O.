<?php

namespace AppBundle\Lasf;

use AppBundle\Entity\Application;
use AppBundle\Entity\Approval;
use AppBundle\Entity\Comment;
use AppBundle\Entity\FileUpload;
use AppBundle\Entity\SubCompetition;
use AppBundle\EventListener\Traits\RecipientTrait;
use AppBundle\Mailer\Mailer;
use Doctrine\ORM\EntityManager;
use AppBundle\Event\DocumentStatusChangeEvent;
use AppBundle\Event\ApplicationStatusChangeEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class ApplicationDocumentHandler
{
    const SUB_COMPETITION = 'sub-competition';

    static public $applicationDocuments = [
        'invoice',
        'signed_application_by_lasf',
        'signed_application_by_organisator',
        'competition_insurance',
        'other_documents',
        'additional_rules',
        'track_acceptance',
        'safety_plan',
        'track_licence',
        'organisator_licence',
        'competition_results',
        'report',
        'bulletin',
        'skk_report',
        'competition_chief_decisions',
    ];


    static public $subCompetitionDocuments = [
        'other_documents',
        'additional_rules',
        'safety_plan',
        'competition_results',
        'report',
        'bulletin',
        'skk_report',
        'competition_chief_decisions',
    ];

    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var EventDispatcherInterface $dispatcher ;
     */
    private $dispatcher;

    /**
     * @var User $user
     */
    private $user;

    /**
     * @var AuthorizationChecker $authorizationChecker
     */
    private $authorizationChecker;

    /**
     * ApplicationDocumentHandler constructor.
     * @param EntityManager $em
     * @param EventDispatcherInterface $dispatcher
     * @param TokenStorage $tokenStorage
     * @param AuthorizationChecker $authorizationChecker
     */
    public function __construct(EntityManager $em, EventDispatcherInterface $dispatcher, TokenStorage $tokenStorage, AuthorizationChecker $authorizationChecker)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param Application $application
     * @param Form $form
     */
    public function handleApplicationDocuments(Application $application, Form $form)
    {
        foreach (self::$applicationDocuments as $type) {
            if ($form->has($type)) {
                foreach ($form->get($type)->getData() as $item) {
                    if ($item) {
                        $document = new FileUpload($type);
                        $document->setUser($this->user);
                        $document->setFile($item);
                        $application->addDocument($document);
                        $this->em->persist($document);

                        switch ($type) {
                            case FileUpload::TYPE_OTHER_DOCUMENTS:
                                $document->setStatus(FileUpload::STATUS_ADDED_OTHER_DOCUMENTS);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document
                                    )
                                );
                                break;

                            case FileUpload::TYPE_ADDITIONAL_RULES:
                                $document->setStatus(FileUpload::STATUS_ADDED_ADDITIONAL_COMPETITION_RULES);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document
                                    )
                                );
                                break;

                            case FileUpload::TYPE_SAFETY_PLAN:
                                $document->setStatus(FileUpload::STATUS_ADDED_SAFETY_PLAN);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document
                                    )
                                );
                                break;

                            case FileUpload::TYPE_COMPETITION_INSURANCE:
                                $document->setStatus(FileUpload::STATUS_ADDED_COMPETITION_INSURANCE);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document
                                    )
                                );
                                break;

                            case FileUpload::TYPE_TRACK_ACCEPTANCE:
                                $document->setStatus(FileUpload::STATUS_ADDED_TRACK_ACCEPTANCE);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document
                                    )
                                );
                                break;

                            case FileUpload::TYPE_TRACK_LICENCE:
                                $document->setStatus(FileUpload::STATUS_ADDED_TRACK_LICENCE);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document
                                    )
                                );
                                break;

                            case FileUpload::TYPE_ORGANISATOR_LICENCE:
                                $document->setStatus(FileUpload::STATUS_ADDED_ORGANISATOR_LICENCE);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document
                                    )
                                );
                                break;

                            case FileUpload::TYPE_SIGNED_APPLICATION_BY_LASF:
                                if (!$application->isPaid()) {
                                    $application->setStatus(Application::STATUS_CONTRACT_UPLOADED_BY_LASF);
                                    $this->dispatcher->dispatch(
                                        'application.status.changed',
                                        new ApplicationStatusChangeEvent($application)
                                    );
                                }
                                break;
                            case FileUpload::TYPE_SIGNED_APPLICATION_BY_ORGANISATOR:
                                if (!$application->isPaid()) {
                                    $application->setStatus(Application::STATUS_CONTRACT_UPLOADED_BY_ORGANISATOR);
                                    $this->dispatcher->dispatch(
                                        'application.status.changed',
                                        new ApplicationStatusChangeEvent($application)
                                    );
                                }
                                break;

                            case FileUpload::TYPE_COMPETITION_RESULTS:
                                $document->setStatus(FileUpload::TYPE_COMPETITION_RESULTS);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document
                                    )
                                );
                                break;

                            case FileUpload::TYPE_COMPETITION_REPORT:
                                $document->setStatus(FileUpload::TYPE_COMPETITION_REPORT);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document
                                    )
                                );
                                break;

                            case FileUpload::TYPE_COMPETITION_BULLETIN:
                                $document->setStatus(FileUpload::TYPE_COMPETITION_BULLETIN);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document
                                    )
                                );
                                break;

                            case FileUpload::TYPE_COMPETITION_SKK_REPORT:
                                $document->setStatus(FileUpload::TYPE_COMPETITION_SKK_REPORT);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document
                                    )
                                );
                                break;
                        }
                    }
                }

                switch ($type) {
                    case FileUpload::TYPE_INVOICE:
                        $application->setStatus(Application::STATUS_NOT_PAID);
                        $this->dispatcher->dispatch(
                            'application.status.changed',
                            new ApplicationStatusChangeEvent($application)
                        );
                        break;
                    default:
                        break;
                }
            }
        }
        if ($application->getSvoDelegate() != null) {
            $application->setSvoDelegateConfirmed(true);
        }
    }

    /**
     * @param SubCompetition $competition
     * @param Form $form
     */
    public function handleSubCompetitionDocuments(SubCompetition $competition, Form $form)
    {
        $application = $competition->getApplication();
        foreach (self::$subCompetitionDocuments as $type) {
            if ($form->has($type)) {
                foreach ($form->get($type)->getData() as $item) {
                    if ($item) {
                        $document = new FileUpload($type);
                        $document->setUser($this->user);
                        $document->setFile($item);
                        $competition->addDocument($document);
                        $this->em->persist($document);

                        switch ($type) {
                            case FileUpload::TYPE_ADDITIONAL_RULES:
                                $document->setStatus(FileUpload::STATUS_ADDED_ADDITIONAL_COMPETITION_RULES);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document,
                                        $competition
                                    )
                                );
                                break;

                            case FileUpload::TYPE_TRACK_ACCEPTANCE:
                                $document->setStatus(FileUpload::STATUS_ADDED_TRACK_ACCEPTANCE);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document,
                                        $competition
                                    )
                                );
                                break;

                            case FileUpload::TYPE_SAFETY_PLAN:
                                $document->setStatus(FileUpload::STATUS_ADDED_SAFETY_PLAN);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document,
                                        $competition
                                    )
                                );
                                break;

                            case FileUpload::TYPE_OTHER_DOCUMENTS:
                                $document->setStatus(FileUpload::STATUS_ADDED_OTHER_DOCUMENTS);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $document
                                    )
                                );
                                break;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param SubCompetition $subCompetition
     * @param FileUpload $document
     */
    public function approveSubCompetitionDocument(SubCompetition $subCompetition, FileUpload $document)
    {
        foreach ($subCompetition->getDocuments() as $doc) {

            if ($doc->getId() == $document->getId()) {
                $this->logApproval($doc);
                switch ($doc->getType()) {

                    case FileUpload::TYPE_ADDITIONAL_RULES:
                        if ($this->authorizationChecker->isGranted('regulations-confirm-secretary', $this->user)) {
                            if ($document->getStatus() == FileUpload::STATUS_ADDED_ADDITIONAL_COMPETITION_RULES) {
                                $document->setStatus(
                                    FileUpload::STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES_BY_SECRETARY
                                );
                            } elseif ($document->getStatus()
                                == FileUpload::STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES_BY_LASF
                            ) {
                                $document->setStatus(FileUpload::STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $subCompetition->getApplication(),
                                        $document,
                                        $subCompetition
                                    )
                                );
                            }
                        }
                        if ($this->authorizationChecker->isGranted('regulations-confirm-lasf', $this->user)) {
                            if ($document->getStatus() == FileUpload::STATUS_ADDED_ADDITIONAL_COMPETITION_RULES) {
                                $document->setStatus(
                                    FileUpload::STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES_BY_LASF
                                );
                            } elseif ($document->getStatus()
                                == FileUpload::STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES_BY_SECRETARY
                            ) {
                                $document->setStatus(FileUpload::STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $subCompetition->getApplication(),
                                        $document,
                                        $subCompetition
                                    )
                                );
                            }
                        }
                        break;

                    case FileUpload::TYPE_SAFETY_PLAN:
                        $document->setStatus(FileUpload::STATUS_CONFIRMED_SAFETY_PLAN);
                        $this->dispatcher->dispatch(
                            'document.status.changed',
                            new DocumentStatusChangeEvent(
                                $subCompetition->getApplication(),
                                $document,
                                $subCompetition
                            )
                        );
                        break;

                    default:
                        break;
                }
            }
        }
        $this->em->flush();
    }

    /**
     * @param Application $application
     * @param FileUpload $fileUpload
     */
    public function approveApplicationDocument(Application $application, FileUpload $fileUpload)
    {
        foreach ($application->getDocuments() as $doc) {
            if ($doc->getId() == $fileUpload->getId()) {
                $this->logApproval($doc);
                switch ($doc->getType()) {

                    case FileUpload::TYPE_ADDITIONAL_RULES:
                        if ($this->authorizationChecker->isGranted('regulations-confirm-secretary', $this->user)) {
                            if ($fileUpload->getStatus() == FileUpload::STATUS_ADDED_ADDITIONAL_COMPETITION_RULES) {
                                $fileUpload->setStatus(
                                    FileUpload::STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES_BY_SECRETARY
                                );
                            } elseif ($fileUpload->getStatus()
                                == FileUpload::STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES_BY_LASF
                            ) {
                                $fileUpload->setStatus(FileUpload::STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $fileUpload
                                    )
                                );
                            }
                        }
                        if ($this->authorizationChecker->isGranted('regulations-confirm-lasf', $this->user)) {
                            if ($fileUpload->getStatus() == FileUpload::STATUS_ADDED_ADDITIONAL_COMPETITION_RULES) {
                                $fileUpload->setStatus(
                                    FileUpload::STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES_BY_LASF
                                );
                            } elseif ($fileUpload->getStatus()
                                == FileUpload::STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES_BY_SECRETARY
                            ) {
                                $fileUpload->setStatus(FileUpload::STATUS_CONFIRMED_ADDITIONAL_COMPETITION_RULES);
                                $this->dispatcher->dispatch(
                                    'document.status.changed',
                                    new DocumentStatusChangeEvent(
                                        $application,
                                        $fileUpload
                                    )
                                );
                            }
                        }
                        break;

                    case FileUpload::TYPE_SAFETY_PLAN:
                        $fileUpload->setStatus(FileUpload::STATUS_CONFIRMED_SAFETY_PLAN);
                        $this->dispatcher->dispatch(
                            'document.status.changed',
                            new DocumentStatusChangeEvent(
                                $application,
                                $fileUpload
                            )
                        );
                        break;

                    case FileUpload::TYPE_TRACK_ACCEPTANCE:
                        $fileUpload->setStatus(FileUpload::STATUS_CONFIRMED_TRACK_ACCEPTANCE);
                        $this->dispatcher->dispatch(
                            'document.status.changed',
                            new DocumentStatusChangeEvent(
                                $application,
                                $fileUpload
                            )
                        );
                        break;

                    case FileUpload::TYPE_TRACK_LICENCE:
                        $fileUpload->setStatus(FileUpload::STATUS_CONFIRMED_TRACK_LICENCE);
                        $this->dispatcher->dispatch(
                            'document.status.changed',
                            new DocumentStatusChangeEvent(
                                $application,
                                $fileUpload
                            )
                        );
                        break;

                    case FileUpload::TYPE_COMPETITION_INSURANCE:
                        $fileUpload->setStatus(FileUpload::STATUS_CONFIRMED_COMPETITION_INSURANCE);
                        $this->dispatcher->dispatch(
                            'document.status.changed',
                            new DocumentStatusChangeEvent(
                                $application,
                                $fileUpload
                            )
                        );
                        break;

                    case FileUpload::TYPE_ORGANISATOR_LICENCE:
                        $fileUpload->setStatus(FileUpload::STATUS_CONFIRMED_ORGANISATOR_LICENCE);
                        $this->dispatcher->dispatch(
                            'document.status.changed',
                            new DocumentStatusChangeEvent(
                                $application,
                                $fileUpload
                            )
                        );
                        break;

                    default:
                        break;
                }
            }
        }

        $this->em->flush();
    }

    /**
     * @param Application $application
     * @param Comment $comment
     */
    public function commentApplicationDocuments(Application $application, Comment $comment)
    {
        switch ($comment->getType()) {
            case Comment::TYPE_ADDITIONAL_RULES:
                $status = FileUpload::STATUS_COMMENTED_ADDITIONAL_COMPETITION_RULES;
                break;

            case Comment::TYPE_SAFETY_PLAN:
                $status = FileUpload::STATUS_COMMENTED_SAFETY_PLAN;
                break;

            default:
                return;
        }

        $document = new FileUpload();
        $document->setStatus($status);
        $document->setComment($comment->getComment());

        $this->dispatcher->dispatch(
            'document.status.changed',
            new DocumentStatusChangeEvent(
                $application,
                $document
            )
        );
    }

    /**
     * @param FileUpload $document
     */
    private function logApproval(FileUpload $document)
    {
        $approval = new Approval();
        $approval->setDocument($document);
        $approval->setUser($this->user);
        $this->em->persist($approval);
    }

    use RecipientTrait;

    /**
     * @param $application
     * @param string $applicationType
     * @param string $documentType
     * @param Mailer $mailer
     */
    public function remindUploadDocument($application, $applicationType, $documentType, Mailer $mailer)
    {
        $organizator = $applicationType == self::SUB_COMPETITION ? $application->getApplication()->getUser() : $application->getUser();
        $recipients = [];

        switch ($documentType) {
            case 'safety_plan':
                if ($application->getCompetitionChief()) {
                    $recipients = array_merge([$organizator, $application->getCompetitionChief()->getUser()], $this->getUsersByRoles([User::$roleMap['ROLE_ADMIN']]));
                } else {
                    $recipients = array_merge([$organizator], $this->getUsersByRoles([User::$roleMap['ROLE_ADMIN']]));
                }
                break;
            case 'additional_rules':
                $recipients = array_merge([$organizator], $this->getUsersByRoles([User::$roleMap['ROLE_SECRETARY'], User::$roleMap['ROLE_ADMIN'], User::$roleMap['ROLE_LASF_COMMITTEE']], $application->getSport()));
                break;
            case 'competition_insurance':
                $recipients = array_merge([$organizator], $this->getUsersByRoles([User::$roleMap['ROLE_SECRETARY'], User::$roleMap['ROLE_ADMIN']]));
                break;
            case 'track_acceptance':
                if ($application->getSvoDelegate()) {
                    $recipients = array_merge([$organizator, $application->getSvoDelegate()], $this->getUsersByRoles([User::$roleMap['ROLE_ADMIN']]));
                } else {
                    $recipients = array_merge([$organizator], $this->getUsersByRoles([User::$roleMap['ROLE_ADMIN']]));
                }
                break;
            case 'signed_application_by_organisator':
                $recipients = array_merge([$organizator], $this->getUsersByRoles([User::$roleMap['ROLE_ADMIN']]));
        }

        $mailer->users($recipients, 'upload_document_to_competition', [
            'competition' => $application->getName(),
            'file' => $documentType,
            'date' => date('Y-m-d', strtotime($application->getDateFrom()->format('Y-m-d')."-40 days")),
        ]);
    }
}
