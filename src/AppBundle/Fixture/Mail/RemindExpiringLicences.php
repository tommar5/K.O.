<?php

namespace AppBundle\Fixture\Mail;

use AppBundle\Entity\MailTemplate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;

class RemindExpiringLicences implements FixtureInterface, OrderedFixtureInterface
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
            'licence_expiring' => [
                'subject'=>'Priminimas apie besibaigiančias licencijas',
                'content'=>'Sveiki,
                    <p>Primename, kad Jūsų licencijos nr. {{ number }} „{{ ("licences.type."~licence.type)|trans }}“ galiojimas baigiasi {{ date|date("Y-m-d") }}. Prašome ją pratęsti.</p>',
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
