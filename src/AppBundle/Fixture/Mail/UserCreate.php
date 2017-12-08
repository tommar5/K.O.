<?php namespace AppBundle\Fixture\Mail;

use AppBundle\Entity\MailTemplate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;

/**
 * Class UserCreate
 * @package AppBundle\Fixture\Mail
 */
class UserCreate implements FixtureInterface, OrderedFixtureInterface
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
            'new_user' => [
                'subject' => 'Vartotojo registracija',
                'content' => 'Sveiki,

<p>norime informuoti, jog esate užregistruotas LASF sistemoje.</p>

<p>Visa informacija, reikalinga prisijungimui prie LASF sistemos yra pateikta žemiau:</p>

<p>
Prisijungimo vardas: <strong>{{ username }}</strong><br>
Slaptažodis: <strong>{{ password }}</strong>
</p>

<p>Prisijungti prie LASF sistemos galite adresu <a href="{{ link }}">{{ link }}</a></p>',
            ],
            'declarant_notify_new_user' => [
                'subject' => 'Vartotojo registracija',
                'content' => 'Sveiki,

<p>norime Jus informuoti apie naujai užregistruotą vartotoją.</p>

<p>Vardas, pavardė: {{ full_name }}</p>

<p>
Prisijungimo vardas: <strong>{{ username }}</strong><br>
Slaptažodis: <strong>{{ password }}</strong>
</p>',
            ],
            'admin_notify_new_user' => [
                'subject' => 'Užregistruotas naujas vartotojas',
                'content' => 'Sveiki,

<p>norime Jus informuoti apie naują vartotoją LASF sistemoje.</p>

<p>
Vardas, pavardė: <strong>{{ full_name }}</strong><br>
Vartotojo rolės: {{ user_role }}
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
