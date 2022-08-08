<?php

namespace yqservice\yqserviceIntegration\responseObjects;

use yqservice\yqserviceIntegration\ResponseObject;

class FilterObject extends ResponseObject
{
    public $fields;
    protected function fromXml($data)
    {
        foreach ($data->row as $filterField) {
            $this->fields[] = new FilterFieldObject($filterField);
        }
    }
}