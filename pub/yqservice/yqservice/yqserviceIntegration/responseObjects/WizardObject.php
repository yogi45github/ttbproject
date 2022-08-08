<?php

namespace yqservice\yqserviceIntegration\responseObjects;

use yqservice\yqserviceIntegration\ResponseObject;

class WizardObject extends ResponseObject
{

    public $steps;

    protected function fromXml($data)
    {
        foreach ($data->row as $step) {
            $this->steps[] = new WizardStepObject($step);
        }
    }
}