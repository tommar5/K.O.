<?php namespace AppBundle\Command;

use AppBundle\Entity\Licence;
use AppBundle\Mailer\Mailer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\Tools\Pagination\Paginator;

class RemindExpiringLicencesCommand extends ContainerAwareCommand
{
    const LIMIT = 100;

    protected function configure()
    {
        $this
            ->setName('app:remind:licences')
            ->setDescription('Sends notifications about expiring licences. Run on XXXX-12-01')
            ->addArgument('days', InputArgument::REQUIRED, 'How much days left to expire licence');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ((int)$input->getArgument('days') < 1) {
            throw new \RuntimeException('You must type an integer that isnt less than 1.');
        }

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('em');

        $statuses = Licence::$completedStatuses;
        if (($key = array_search(Licence::STATUS_EXTEND, $statuses)) !== false) { // don't remind if extending
            unset($statuses[$key]);
        }

        $licences = $em->getRepository(Licence::class)->createQueryBuilder('l')
            ->where('l.status IN(:status)')
            ->andWhere($em->getExpressionBuilder()->eq('DATE_DIFF(l.expiresAt, CURRENT_DATE())', ':DAYS'))
            ->setParameters(['status' => $statuses, 'DAYS' => $input->getArgument('days')])
            ->getQuery();

        /** @var Mailer $mailer */
        $mailer = $this->getContainer()->get('mail');

        $paginator = new Paginator($licences);

        $total = $paginator->count();
        $maxBatches = ceil($total / self::LIMIT);

        $batch = 1;

        if ($total) {
            do {
                $paginator->getQuery()
                    ->setFirstResult(self::LIMIT * ($batch - 1))
                    ->setMaxResults(self::LIMIT);

                foreach ($paginator->getIterator() as $item) {
                    $mailer->user($item->getUser(), 'licence_expiring', [
                        'licence' => $item,
                        'number' => $item->getLicenceNumber(),
                        'date' => $item->getExpiresAt()
                    ]);
                }
                $batch++;
            } while ($batch <= $maxBatches);
        }
    }
}
