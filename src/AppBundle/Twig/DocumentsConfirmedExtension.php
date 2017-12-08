<?php

namespace AppBundle\Twig;

class DocumentsConfirmedExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('documentsConfirmed', [$this, 'documentsConfirmed']),
        ];
    }

    /**
     * @param $documents
     * @param array $confirmed
     * @return bool
     */
    public function documentsConfirmed($documents, array $confirmed)
    {
        if (count($documents) > 0) {
            foreach ($documents as $document) {
                if (!$document->hasStatus($confirmed)) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'documentsConfirmed';
    }
}
