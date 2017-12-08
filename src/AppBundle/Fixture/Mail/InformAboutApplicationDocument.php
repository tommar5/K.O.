<?php

namespace AppBundle\Fixture\Mail;

use AppBundle\Entity\MailTemplate;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;

class InformAboutApplicationDocument implements FixtureInterface, OrderedFixtureInterface
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
            'inform_about_added_additional_competition_rules' => [
                'subject'=>'Pranešimas apie įkeltas papildomas varžybų nuostatas',
                'content'=>'Sveiki,
                    <p>Sistemoje atsirado naujai įkelti varžybų nuostatai.</p>
                    Pareiškėjas: {{ customer }}</br>
                    Paraiškos pavadinimas: {{ application }}</br>
                    ',
            ],
            'inform_about_commented_additional_competition_rules' => [
                'subject'=>'Pranešimas apie pakomentuotą papildomų nuostatų dokumentą',
                'content'=>'Sveiki,
                    <p>Sistemoje atsirado naujas papildomų nuostatų dokumentų komenentaras.</p>
                    Paraiškos pavadinimas: {{ application }}</br>
                    Komentaras: {{ comment }}</br>
                    ',
            ],
            'inform_about_confirmed_additional_competition_rules' => [
                'subject'=>'Pranešimas apie patvirtintą papildomų nuostatų dokumentą',
                'content'=>'Sveiki,
                    <p>Jūsų papildomi nuostatų dokumentai buvo patvirtinti.</p>
                    Paraiškos pavadinimas: {{ application }}</br>
                    ',
            ],
            'inform_about_added_safety_plan' => [
                'subject'=>'Pranešimas apie pridėtą saugos planą',
                'content'=>'Sveiki,
                    <p>Pranešame, kad buvo pridėtas naujas saugos planas.</p>
                    Paraiškos pavadinimas: {{ application }}</br>
                    ',
            ],
            'inform_about_commented_safety_plan' => [
                'subject'=>'Pranešimas apie naują komentarą prie saugos plano dokumentų',
                'content'=>'Sveiki,
                    <p>Pranešame, kad buvo pridėtas naujas komentaras prie saugos plano dokumentų.</p>
                    Paraiškos pavadinimas: {{ application }}</br>
                    Komentaras: {{ comment }}</br>
                    ',
            ],
            'inform_about_confirmed_safety_plan' => [
                'subject'=>'Pranešimas apie patvirtintą saugos planą',
                'content'=>'Sveiki,
                    <p>Pranešame, kad Jūsų pateiktas saugos planas buvo patvirtintas SVO komiteto.</p>
                    Paraiškos pavadinimas: {{ application }}</br>
                    ',
            ],
            'inform_added_competition_insurance' => [
                'subject'=>'Pranešimas apie pridėtus varžybų draudimo dokumentus',
                'content'=>'Sveiki,
                    <p>Pranešame, kad buvo pateikti varžybų draudimo dokumentai.</p>
                    Paraiškos pavadinimas: {{ application }}</br>
                    ',
            ],
            'inform_added_other_documents' => [
                'subject'=>'Pranešimas apie pridėtus papildomus varžybų dokumentus',
                'content'=>'Sveiki,
                    <p>Pranešame, kad buvo pateikti papildomi varžybų organizavimo dokumentai.</p>
                    Paraiškos pavadinimas: {{ application }}</br>
                    ',
            ],
            'inform_added_track_acceptance' => [
                'subject'=>'Pranešimas apie pridėtą trasos priėmimo aktą',
                'content'=>'Sveiki,
                    <p>Pranešame, kad buvo pateiktas trasos priėmimo aktas..</p>
                    Paraiškos pavadinimas: {{ application }}</br>
                    ',
            ],
            'inform_about_confirmed_track_acceptance' => [
                'subject'=>'Pranešimas apie patvirtintą trasos priėmimo aktą',
                'content'=>'Sveiki,
                    <p>Pranešame, kad Jūsų pateiktas trasos priėmimo aktas buvo patvirtintas SVO komiteto.</p>
                    Paraiškos pavadinimas: {{ application }}</br>
                    ',
            ],
            'inform_about_confirmed_track_licence' => [
                'subject'=>'Pranešimas apie patvirtintą trasos licenciją',
                'content'=>'Sveiki,
                    <p>Pranešame, kad Jūsų pateikta licencija buvo patvirtinta.</p>
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