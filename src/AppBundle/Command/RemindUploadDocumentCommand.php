<?php namespace AppBundle\Command;

use AppBundle\Entity\Application;
use AppBundle\Entity\FileUpload;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class RemindUploadDocumentCommand extends ContainerAwareCommand
{
    const LIMIT = 50;

    const RequiredDocumentForApplication = [
        FileUpload::TYPE_ADDITIONAL_RULES,
        FileUpload::TYPE_SAFETY_PLAN,
        FileUpload::TYPE_COMPETITION_INSURANCE,
    ];

    const ApplicationStatus = [
        Application::STATUS_CONTRACT_UPLOADED_BY_LASF,
        Application::STATUS_CONTRACT_UPLOADED_BY_ORGANISATOR,
        Application::STATUS_CONFIRMED,
        Application::STATUS_NOT_PAID,
    ];

    /**
     * @var EntityManager $em
     */
    private $em;

    protected function configure()
    {
        $this
            ->setName('app:remind:application')
            ->setDescription('Sends notifications about upload document to application. Should be run daily.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $this->em = $this->getContainer()->get('em');

        $applications = $this->em->getRepository(Application::class)->getApplicationsThatBeginsBefore(self::LIMIT, self::ApplicationStatus);

        foreach ($applications as $application) {
            $this->processDocuments($application);
        }
    }

    /**
     * @param Application $application
     */
    public function processDocuments(Application $application)
    {
        $uploadedDocuments = [];

        foreach ($application->getDocuments() as $key => $document) {
            if (in_array($document->getType(), self::RequiredDocumentForApplication)) {
                $uploadedDocuments[$key] = $document->getType();
            }
        }

        $this->sendTo($application, $uploadedDocuments);
    }

    /**
     * @param Application $competition
     * @param array $documents
     */
    public function sendTo(Application $competition, array $documents)
    {
        $mailer = $this->getContainer()->get('mail');
        $neededDocuments = array_diff(self::RequiredDocumentForApplication, $documents);
        $recipients = array_merge($this->em->getRepository(User::class)->getAdmins(), [$competition->getUser()]);

        foreach ($neededDocuments as $document) {

            $mailer->users($recipients, 'upload_document_to_competition', [
                'competition' => $competition->getName(),
                'file' => $document,
                'date' => date('Y-m-d', strtotime("+10 day")),
            ]);
        }
    }
}
