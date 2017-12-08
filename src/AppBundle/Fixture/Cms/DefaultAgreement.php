<?php

namespace AppBundle\Fixture\Cms;

use AppBundle\Entity\CmsBlock;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DefaultAgreement implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
        return 0;
    }

    private function resource($name)
    {
        $location = $this->container->get('kernel')->locateResource("@AppBundle/Resources/views/blocks/layout/{$name}.html.twig");
        return file_get_contents($location);
    }

    public function load(ObjectManager $manager)
    {
        $blocks = [
            'default_agreement'
        ];

        foreach ($blocks as $alias) {
            $block = new CmsBlock();
            $block->setAlias($alias);
            $block->setName(implode(' ', array_map('ucfirst', explode('_', $alias))));
            $block->setContent($this->resource($alias));
            $manager->persist($block);
        }

        $manager->flush();
    }
}
