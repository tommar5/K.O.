<?php namespace AppBundle\Fixture\Mail;

use AppBundle\Entity\MailTemplate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

class UserConfirm implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $emails = [
            'user_confirmed' => [
                'subject' => 'Registracija patvirtinta',
                'content' => 'Sveiki,

<p>norime informuoti, jog jūsų registruotas vartotojas LASF sistemoje patvirtintas.</p>

<p>Prisijungti prie LASF sistemos galite adresu <a href="{{ link }}">{{ link }}</a></p>',
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
