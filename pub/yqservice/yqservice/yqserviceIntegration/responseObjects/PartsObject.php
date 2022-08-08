<?php
/**
 * Created by YQService.
 * User: YQService
 * Date: 08.05.18
 * Time: 15:04
 */

namespace yqservice\yqserviceIntegration\responseObjects;

use yqservice\yqserviceIntegration\ResponseObject;
use yqservice\yqserviceIntegration\responseObjects\OemPartObject;
use SimpleXMLElement;

class PartsObject extends ResponseObject
{
    public $oemParts = [];

    protected function fromXml($data) {
        foreach ($data->OEMPart as $part) {
            $partObj = new OemPartObject($part);
            $this->oemParts[] = $partObj;
        }
    }

}