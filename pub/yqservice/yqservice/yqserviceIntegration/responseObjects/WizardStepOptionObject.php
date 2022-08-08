<?php

namespace yqservice\yqserviceIntegration\responseObjects;

use yqservice\yqserviceIntegration\ResponseObject;

class WizardStepOptionObject extends ResponseObject
{

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $value;

    protected function fromXml($data)
    {
        $this->key = (string)$data['key'];
        $this->value = html_entity_decode($data['value']);
    }
}