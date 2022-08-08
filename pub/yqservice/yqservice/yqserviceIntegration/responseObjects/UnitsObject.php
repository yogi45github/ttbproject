<?php

namespace yqservice\yqserviceIntegration\responseObjects;

use yqservice\yqserviceIntegration\ResponseObject;

class UnitsObject extends ResponseObject
{

    /**
     * @var UnitObject[]
     */
    public $units;

    protected function fromXml($data)
    {
        foreach ($data->row as $unit) {
            $current = new UnitObject($unit);
            $this->units[] = $current;
        }
    }
}