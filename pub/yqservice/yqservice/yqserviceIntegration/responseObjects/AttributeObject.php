<?php

namespace yqservice\yqserviceIntegration\responseObjects;

use yqservice\yqserviceIntegration\ResponseObject;

class AttributeObject extends ResponseObject
{
    public $key;

    public $name;

    public $value;

    protected function fromXml($data)
    {
        $this->key = (string)$data['key'];
        $this->name = (string)$data['name'];
        $this->value = (string)$data['value'];
    }

    protected function fromJSON($data)
    {
        $this->fromXml($data);
    }
}