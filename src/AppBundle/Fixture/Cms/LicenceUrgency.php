<?php

namespace AppBundle\Fixture\Cms;

use AppBundle\Entity\CmsBlock;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LicenceUrgency implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 0;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $blocks = [
            'urgency_standard' => 'Įprastas licencijos išdavimas (per 5 darbo dienas)',
            'urgency_urgent' => 'Skubus licencijos išdavimas (taikomas papildomas 30% mokestis nuo licencijos kainos)',
        ];

        foreach ($blocks as $key => $content) {
            $block = new CmsBlock();
            $block->setAlias($key);
            $block->setName(implode(' ', array_map('ucfirst', explode('_', $key))));
            $block->setContent($content);
            $manager->persist($block);
        }

        $manager->flush();
    }
}
