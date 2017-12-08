<?php

namespace AppBundle\Fixture\Mail;

use AppBundle\Entity\MailTemplate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;

class InformAboutNewLicence implements FixtureInterface, OrderedFixtureInterface
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
            'inform_about_licence' => [
                'subject'=>'Pranešimas apie naują licenciją',
                'content'=>'Sveiki,
                    <p>Norime pranešti apie naują licencijos prašymą LASF sistemoje.</p>

                    <p>
                        Prašymą pateikė: {{ customer }}<br>
                        Pageidaujama licencija: {{ ("licences.type."~licence)|trans }}
                    </p>
                    ',
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