<?php

namespace AppBundle\Fixture\Languages;

use AppBundle\Entity\Language;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class DefaultLanguages implements FixtureInterface, OrderedFixtureInterface
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
        $languages = [
            'Lietuvių',
            'Rusų',
            'Anglų',
            'Prancūzų',
            'Kita',
        ];

        foreach ($languages as $language) {
            $languageObject = new Language();
            $languageObject->setLanguage($language);
            $manager->persist($languageObject);
        }

        $manager->flush();
    }

}
