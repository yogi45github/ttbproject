<?php
/**
 * Created by PhpStorm.
 * User: applebred
 * Date: 10.01.19
 * Time: 15:30
 */

namespace yqservice\yqserviceIntegration\responseObjects;


use yqservice\yqserviceIntegration\ResponseObject;
use SimpleXMLElement;

class AMDetailsObject extends ResponseObject
{
    /**
     * @var array $oems
     */
    public $oems;

    /**
     * @param SimpleXMLElement $data
     */
    protected function fromXml($data)
    {
        foreach ($data as $detail) {
            $detail = new AMDetailObject($detail);
            $this->oems[] = $detail;
        }
    }
}