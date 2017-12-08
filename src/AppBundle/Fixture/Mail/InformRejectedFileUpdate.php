<?php

namespace AppBundle\Fixture\Mail;

use AppBundle\Entity\MailTemplate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;

class InformRejectedFileUpdate implements FixtureInterface, OrderedFixtureInterface
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
            'inform_rejected_file_update' => [
                'subject'=>'Pranešimas apie atmesto dokumento atnaujinimą',
                'content'=>'Sveiki,
                    <p>Norime pranešti naujo dokumento įkėlimą LASF sistemoje.</p>
                    <p>
                        Vartotojas: {{ customer }}<br>
                        Licencija: {{ ("licences.type."~licence)|trans }}<br>
                        Dokumentas: {{ ("file_uploads.type."~file)|trans }}
                    </p>',
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