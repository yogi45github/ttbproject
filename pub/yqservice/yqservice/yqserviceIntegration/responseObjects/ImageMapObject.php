<?php

namespace yqservice\yqserviceIntegration\responseObjects;

use yqservice\yqserviceIntegration\ResponseObject;

class ImageMapObject extends ResponseObject
{
    public $mapObjects;

    protected function fromXml($data)
    {
        foreach ($data->row as $mapObject) {
            $this->mapObjects[] = new MapObject($mapObject);
        }
    }
}