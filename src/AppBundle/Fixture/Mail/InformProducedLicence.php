<?php

namespace AppBundle\Fixture\Mail;

use AppBundle\Entity\MailTemplate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;

class InformProducedLicence implements FixtureInterface, OrderedFixtureInterface
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
            'inform_produced_licence' => [
                'subject'=>'Pranešimas apie pagamintą licenciją',
                'content'=>'Sveiki,
                    <p>Norime pranešti, jog jūsų licencija "{{ ("licences.type."~licence)|trans }}" buvo pagaminta.</p>',
            ],
            'inform_paid_licence' => [
                'subject'=>'Pranešimas apie apmokėtą licenciją',
                'content'=>'Sveiki,
                    <p>Sistemoje atsirado apmokėta {{ ("licences.type."~licence.type)|trans }} licencija.</p>

                    <p>Klubas: {% if licence.driverLicence %}
                      {{ licence.licence ? licence.licence.user.memberName }}
                    {% elseif licence.membershipLicence or licence.organisatorLicence or licence.declarantLicence or licence.trackLicence %}
                      {{ licence.user.memberName }}
                    {% elseif licence.judgeLicence %}
                      {{ licence.declarant.memberName }}
                    {% endif %}</p>

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
