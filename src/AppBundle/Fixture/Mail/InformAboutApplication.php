<?php

namespace AppBundle\Fixture\Mail;

use AppBundle\Entity\MailTemplate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;

class InformAboutApplication implements FixtureInterface, OrderedFixtureInterface
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
            'inform_about_application' => [
                'subject'=>'Pranešimas apie naują paraišką',
                'content'=>'Sveiki,
                    <p>Norime pranešti apie naują varžybų paraišką LASF sistemoje.</p>

                    <p>
                        Prašymą pateikė: {{ customer }}<br>
                    </p>
                    ',
            ],
            'inform_paid_application' => [
                'subject'=>'Pranešimas, kad jūsų apmokėjimas buvo patvirtintas',
                'content'=>'Sveiki,
                    <p>Pranešame, kad buvo gautas jūsų apmokėjimas.</p>
                    <p>Prašome įkelti likusius dokumentus, nurodytus paraiškoje.</p>
                    Paraiškos pavadinimas: {{ application }}</br>
                    ',
            ],
            'inform_about_confirmed_application' => [
                'subject'=>'Jūsų paraiškos informacija buvo patvirtinta',
                'content'=>'Sveiki,
                    <p>Jūsų paraiškos informacija buvo patvirtinta</p>
                    
                    Sistemoje rasite sąskaitą apmokėjimui.</br>
                    Paraiškos pavadinimas: {{ application }}
                    ',
            ],
            'inform_about_declined_application' => [
                'subject'=>'Jūsų paraiškos informacija buvo atmesta',
                'content'=>'Sveiki,
                    <p>Jūsų paraiškos informacija buvo atmesta</p>
                    Paraiškos pavadinimas: {{ application }}</br>
                    Priežastis: {{ reason }}
                    ',
            ],
            'inform_about_invoice_application' => [
                'subject'=>'Jums buvo pateikta sąskaita',
                'content'=>'Sveiki,
                    <p>Prašome apmokėti Jums pateiktą sąskaitą.</p>
                    Paraiškos pavadinimas: {{ application }}</br>
                    Sąskaita parsisiuntimui: <a href="{{ app.request.scheme ~\'://\' ~ app.request.httpHost ~ \'/uploads/\' ~ invoice }}">{{invoice}}</a>
                    </br>
                    ',
            ],
            'inform_about_uploaded_contract_by_lasf' => [
                'subject'=>'Jums buvo sugeneruota sutartis',
                'content'=>'Sveiki,
                    <p>Jums buvo sugeneruota sutartis prašome ją pasirašyti ir įkelti prie savo paraiškos.</p>
                    <p>Sutartį galite rasti prie savo pateiktos paraiškos.</p>
                    Paraiškos pavadinimas: {{ application }}</br>
                    ',
            ],
            'inform_about_uploaded_contract_by_organisator' => [
                'subject'=>'Pranešimas apie pasirašytą sutartį',
                'content'=>'Sveiki,
                    <p>Sistemoje atsirado nauja įkelta sutartis.</p>
                    Paraiškos pavadinimas: {{ application }}</br>
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