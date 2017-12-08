<?php

namespace AppBundle\Fixture\Mail;

use AppBundle\Entity\MailTemplate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;

class InformRejectedFile implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 0;
    }

    public function load(ObjectManager $manager)
    {
        $emails = [
            'inform_rejected_file' => [
                'subject'=>'Pranešimas apie atmestą dokumentą',
                'content'=>'Sveiki,
                    <p>Norime pranešti, kad "{{ ("licences.type."~licence)|trans }}" licencijai pateiktas {{ ("file_uploads.type."~file)|trans }} dokumentas buvo atmestas. Prašome įkelti naują dokumentą.</p>',
            ],
        ];

        foreach ($emails as $alias => $emailData) {
            $email = new MailTemplate();
            $email->setAlias($alias);
            $email->setSubject($emailData['subject']);
            $email->setContent($emailData['content']);
            $manager->persist($email);
        }

        $manager->flush();
    }
}