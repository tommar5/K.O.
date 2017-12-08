<?php

namespace AppBundle\Twig;

class BackButtonExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $defaults = [
            'is_safe' => ['html'],
            'needs_environment' => true,
        ];

        return [
            new \Twig_SimpleFunction('back_button', [$this, 'back'], $defaults),
        ];
    }

    /**
     * @param \Twig_Environment $twig
     * @param string $route
     * @return string
     */
    public function back(\Twig_Environment $twig, $route)
    {
        return $twig->render("AppBundle::back_button.html.twig", compact('route'));
    }

    public function getName()
    {
        return 'back_button_extension';
    }
}
