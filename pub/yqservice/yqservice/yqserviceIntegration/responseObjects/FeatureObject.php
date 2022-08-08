<?php

namespace yqservice\yqserviceIntegration\responseObjects;

use yqservice\yqserviceIntegration\ResponseObject;

class FeatureObject extends ResponseObject
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $example;

    protected function fromXml($data)
    {
        $this->example = isset($data['example']) ? (string)$data['example'] : null;
        $this->name = (string)$data['name'];
    }
}