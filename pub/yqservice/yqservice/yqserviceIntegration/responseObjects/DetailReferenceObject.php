<?php
/**
 * Created by PhpStorm.
 * User: applebred
 * Date: 16.01.19
 * Time: 15:54
 */

namespace yqservice\yqserviceIntegration\responseObjects;


use yqservice\yqserviceIntegration\ResponseObject;
use SimpleXMLElement;

class DetailReferenceObject extends ResponseObject
{
    /**
     * @var string $brand
     */
    public $brand;

    /**
     * @var string $code
     */
    public $code;

    /**
     * @param SimpleXMLElement $data
     */
    protected function fromXml($data)
    {
        $this->brand = (string)$data->attributes()->brand;
        $this->code  = (string)$data->attributes()->code;
    }

    protected function fromJSON($data) {
        $this->brand = (string) $data['catalog']->attributes()->brand;
        $this->code  = (string)$data['catalog']->attributes()->code;
    }
}