<?php

namespace AppBundle\Fixture\Mail;

use AppBundle\Entity\MailTemplate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;

class RemindExpiringDocuments implements FixtureInterface, OrderedFixtureInterface
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
            'document_expiring' => [
                'subject'=>'Priminimas apie dokumento galiojimą',
                'content'=>'Sveiki,
                    <p>Primename, kad Jūsų licencijos nr. {{ number }} „{{ ("licences.type."~licence.type)|trans }}“ dokumento „{{ (\'file_uploads.type.\'~file.type)|trans }}“ galiojimas baigiasi {{ date|date("Y-m-d") }}.</p>',
            ],
            'document_expiring_admin' => [
                'subject'=>'Dokumento galiojimas baigiasi',
                'content'=>'Sveiki,
                    <p>Primename, kad vartotojo „{{ customer.fullName }}“ licencijos nr. {{ number }} „{{ ("licences.type."~licence.type)|trans }}“ dokumento „{{ (\'file_uploads.type.\'~file.type)|trans }}“ galiojimas baigiasi {{ date|date("Y-m-d") }}.</p>',
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
