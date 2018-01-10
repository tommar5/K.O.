<?php

namespace AppBundle\Fixture\Applications;

use AppBundle\Entity\MusicStyle;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Sports implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 10;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $sports = [
            ['name' => 'Ralis', 'alias' => 'rally'],
            ['name' => 'Ralis-sprintas', 'alias' => 'rally-sprint'],
            ['name' => 'Žiedas', 'alias' => 'cycle'],
            ['name' => 'Krosas', 'alias' => 'cross'],
            ['name' => 'Ralio-krosas', 'alias' => 'rally-cross'],
            ['name' => 'Driftas', 'alias' => 'drift'],
            ['name' => 'Dragas', 'alias' => 'drag'],
            ['name' => '4x4', 'alias' => '4x4'],
            ['name' => 'Slalomas', 'alias' => 'slalom'],
            ['name' => 'GSKL', 'alias' => 'gskl'],
            ['name' => 'Trekas', 'alias' => 'trek'],
            ['name' => 'Kartingai', 'alias' => 'karting'],
            ['name' => 'Kitos sporto šakos', 'alias' => 'other'],
        ];

        foreach ($sports as $sportElement) {
            $sport = new MusicStyle();
            $sport->setName($sportElement['name']);
            $sport->setAlias($sportElement['alias']);

            $em->persist($sport);
        }
        $em->flush();
    }
}
