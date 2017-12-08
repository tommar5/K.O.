<?php namespace AppBundle\Command;

use AppBundle\Entity\FileUpload;
use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\Tools\Pagination\Paginator;

class RemindExpiringDocumentsCommand extends ContainerAwareCommand
{
    const licencesStatus = [
        Licence::STATUS_PAID,
        Licence::STATUS_PRODUCED,
    ];

    const fileType = [
        FileUpload::TYPE_DRIVERS_LICENCE,
        FileUpload::TYPE_MED_CERT,
        FileUpload::TYPE_INSURANCE,
    ];

    const LIMIT = 100;

    protected function configure()
    {
        $this
            ->setName('app:remind:documents')
            ->setDescription('Sends notifications about expiring documents. Shall be run daily.')
            ->addArgument('days', InputArgument::REQUIRED, 'How much days left to expire document');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if ((int)$input->getArgument('days') < 1) {
            throw new \RuntimeException('You must type an integer that isnt less than 1.');
        }

        $em = $this->getContainer()->get('em');

        $licences = $em->getRepository(Licence::class)->createQueryBuilder('l')
            ->addSelect('ld')
            ->join('l.documents', 'ld')
            ->where($em->getExpressionBuilder()->andX(
                $em->getExpressionBuilder()->eq('DATE_DIFF(ld.validUntil, CURRENT_DATE())', ':DAYS'),
                $em->getExpressionBuilder()->in('ld.type', ':types'),
                $em->getExpressionBuilder()->in('l.status', ':status')
            ))
            ->setParameters([
                'DAYS' => $input->getArgument('days'),
                'types' => self::fileType,
                'status' => self::licencesStatus,
            ])
            ->getQuery();

        $mailer = $this->getContainer()->get('mail');

        $admins = $em->getRepository(User::class)->getAdmins();

        $paginator = new Paginator($licences);

        $total = $paginator->count();
        $maxBatches = ceil($total/self::LIMIT);

        $batch = 1;

        if ($total) {
            do {
                $paginator->getQuery()
                    ->setFirstResult(self::LIMIT * ($batch - 1))
                    ->setMaxResults(self::LIMIT);

                foreach ($paginator->getIterator() as $item) {
                    foreach ($item->getDocuments() as $document) {
                        if (in_array($document->getType(), self::fileType)) {
                            $mailer->users($admins, 'document_expiring_admin', [
                                'customer' => $item->getUser(),
                                'licence' => $item,
                                'number' => $item->getLicenceNumber(),
                                'file' => $document,
                                'date' => $document->getValidUntil(),
                            ]);
                            $mailer->user($item->getUser(), 'document_expiring', [
                                'licence' => $item,
                                'number' => $item->getLicenceNumber(),
                                'file' => $document,
                                'date' => $document->getValidUntil(),
                            ]);
                        }
                    }
                }
                $batch++;
            } while ($batch <= $maxBatches);
        }
    }
}
