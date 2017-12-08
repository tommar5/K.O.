<?php namespace AppBundle\Command;

use AppBundle\Entity\Licence;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class GenerateLicencesNumbersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:generate:series')
            ->setDescription('Generate numbers of judges licences');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $licences = $this->getContainer()->get('em')->getRepository(Licence::class)->createQueryBuilder('l')
            ->where('l.status IN(:status)')
            ->andWhere("l.series is NULL OR l.series = ''")
            ->andWhere('l.type IN(:types)')
            ->setParameter('status', Licence::$completedStatuses)
            ->setParameter('types', Licence::$judgeTypes)
            ->getQuery()->getResult();
        foreach ($licences as $licence) {
            $this->getContainer()->get('lasf.licence_serial_code')->createSerialNumber($licence);
        }
        $this->getContainer()->get('em')->flush();
    }
}
