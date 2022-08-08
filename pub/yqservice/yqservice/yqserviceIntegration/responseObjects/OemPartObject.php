<?php
/**
 * Created by YQService.
 * User: YQService
 * Date: 08.05.18
 * Time: 15:15
 */

namespace yqservice\yqserviceIntegration\responseObjects;


use yqservice\yqserviceIntegration\ResponseObject;

class OemPartObject extends ResponseObject
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $oem;

    protected function fromXml($data) {
        $this->name = (string) $data->name;
        $this->oem  = (string) $data->attributes()->oem;
    }
}