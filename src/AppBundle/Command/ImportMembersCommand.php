<?php namespace AppBundle\Command;

use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ImportMembersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:members:import')
            ->setDescription('Import members from CSV.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $registrator = $this->getContainer()->get('app.registrator');
        $em = $this->getContainer()->get('em');

        if (($handle = fopen($this->getContainer()->getParameter('kernel.root_dir') . '/../nariai.csv', "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $names = explode(' ', $data[4]);

                $output->write($data[1] . "\t");

                $output->write($data[9] . "\t");

                $user = new User();
                $user->setEmail($data[9]);
                $user->setMemberName($data[1]);
                $user->setMemberCode($data[6]);
                if ($data[7]) {
                    $user->setAddress($data[7]);
                }
                $user->setAssociated($data[2] == 'Asocijuoti');
                $user->setLegal(true);
                $user->setFirstname($names[0]);
                $user->setLastname(trim($names[1], " ,"));
                $user->setReceiveNotifications(false);

                if ($data[8]) {
                    $user->setPhone('+' . $data[8]);
                }

                $pass = $registrator->createRandomString(8);

                $output->write($pass . "\t");

                $user->setPlainPassword($pass);
                $registrator->registerUser($user, $user);
                $user->addRole('ROLE_DECLARANT');

                $errorList = $this->getContainer()->get('validator')->validate($user);
                if (0 === count($errorList)) {
                    $em->persist($user);

                    $licence = new Licence();
                    $licence->setUser($user);
                    $licence->setStatus(Licence::STATUS_PRODUCED);
                    $licence->setType(Licence::TYPE_MEMBERSHIP);
                    $licence->setPersonalCode($user->isAssociated());
                    $licence->setExpiresAt(new \DateTime(date('Y-12-31')));


                    $em->persist($licence);
                } else {
                    $output->write("\n");
                    foreach ($errorList as $err) {
                        $output->write($err->getMessage());
                    }
                }
                $output->write("\n");
            }
            fclose($handle);
        }

        $em->flush();

        $licences = $em->getRepository(Licence::class)->createQueryBuilder('l')
            ->where('l.series is null')
            ->getQuery()->getResult();

        foreach ($licences as $l) {
            $this->getContainer()->get('lasf.licence_serial_code')->createSerialNumber($l);
            $em->flush();
        }
    }
}
