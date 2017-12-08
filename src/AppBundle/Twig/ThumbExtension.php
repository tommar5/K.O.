<?php namespace AppBundle\Twig;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class ThumbExtension
 */
class ThumbExtension extends \Twig_Extension
{
    /**
     * @var UploaderHelper
     */
    private $helper;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var
     */
    private $config;

    /**
     * ThumbExtension constructor.
     * @param UploaderHelper $helper
     * @param CacheManager $cacheManager
     * @param FilterConfiguration $config
     */
    public function __construct(UploaderHelper $helper, CacheManager $cacheManager, FilterConfiguration $config)
    {
        $this->helper = $helper;
        $this->cacheManager = $cacheManager;
        $this->config = $config;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'thumb';
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('thumb', [$this, 'thumb'])
        ];
    }

    /**
     * @param Object $entity
     * @param string $fieldName
     * @param string $filterName
     * @return string
     */
    public function thumb($entity, $fieldName, $filterName)
    {
        $path = $this->helper->asset($entity, $fieldName);

        if (!$path && $cfg = $this->config->get($filterName)) {
            if (isset($cfg['default_image'])) {
                $path = $cfg['default_image'];
            }
        }

        if (!$path) {
            $path = "/build/img/blank-photo.jpeg";
        }

        return $this->cacheManager->getBrowserPath($path, $filterName);
    }
}
