<?php

namespace AppBundle\Fixture\Mail;

use AppBundle\Entity\MailTemplate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;

class RemindUploadDocumentToApplication implements FixtureInterface, OrderedFixtureInterface
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
            'upload_document_to_competition' => [
                'subject'=>'Priminimas įkelti reikiamus dokumentus paraiškai',
                'content'=>'Sveiki,
                    <p>Primename, kad paraiškoje „{{ competition }}“ nėra pateiktas dokumentas „{{ (\'file_uploads.type.\'~file)|trans }}“. Dokumentas turi būti pateiktas iki {{ date }}.</p>'
            ],
            'upload_document_to_competition_without_date' => [
                'subject'=>'Priminimas įkelti reikiamus dokumentus paraiškai',
                'content'=>'Sveiki,
                    <p>Primename, kad paraiškoje „{{ competition }}“ nėra pateiktas dokumentas „{{ (\'file_uploads.type.\'~file)|trans }}“. Prašom kaip įmanoma skubiau įkelti dokumentą.</p>'
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
