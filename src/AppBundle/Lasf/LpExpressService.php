<?php

namespace AppBundle\Lasf;

class LpExpressService
{
    /**
     * @var string
     */
    private $lpExpressKey;

    /**
     * @param $key
     */
    public function __construct($key)
    {
        $this->lpExpressKey = $key;
    }

    /**
     * @return array
     */
    public function getLpExpressList()
    {
        //Return data from XML
        $lpExpressList = [];
        $lpExpress = simplexml_load_file($this->lpExpressKey);

        foreach ($lpExpress->data->item as $lpData) {
            $lpExpressList[] = $lpData->address->streethouse . ', ' . $lpData->address->city . ', '
                . $lpData->address->postcode . ', ' . $lpData->name . ' - ' . $lpData->comment;
        }

        return $lpExpressList;
    }
}
