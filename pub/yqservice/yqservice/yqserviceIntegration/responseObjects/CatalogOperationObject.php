<?php

namespace yqservice\yqserviceIntegration\responseObjects;

use yqservice\yqserviceIntegration\ResponseObject;
use SimpleXMLElement;

class CatalogOperationObject extends ResponseObject
{

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $kind;

    /**
     * @var string
     */
    public $name;

    /**
     * @var CatalogOperationFieldObject[]
     */
    public $fields;

    /**
     * @param SimpleXMLElement $data
     */
    protected function fromXml($data)
    {
        $this->description = (string)$data['description'];
        $this->kind = (string)$data['kind'];
        $this->name = (string)$data['name'];
        foreach ($data->field as $field) {
            $this->fields[] = new CatalogOperationFieldObject($field);
        }
    }
}