<?php

namespace AppBundle\Twig;

class StatusChangeExtension extends \Twig_Extension
{

    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PENDING = 'pending';

    /**
     * @param $documents
     * @param array $confirmed
     * @param array $added
     * @return string
     */
    public function statusChange($documents, array $confirmed, array $added)
    {
        $status = null;
        foreach ($documents as $document) {
            if (in_array($document->getStatus(), $confirmed)) {
                $status = $status == null ? self::STATUS_CONFIRMED : $status;
            } elseif (in_array($document->getStatus(), $added)) {
                $status = self::STATUS_PENDING;
            } else {
                return '';
            }
        }
        return $status;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('statusChange', [$this, 'statusChange'])
        ];
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'statusChange';
    }
}
