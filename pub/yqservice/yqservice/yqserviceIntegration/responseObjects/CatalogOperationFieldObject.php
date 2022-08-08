<?php
/**
 * Created by YQService
 * User: altunint
 * Date: 4/9/18
 * Time: 12:16 PM
 * TasK:
 */

namespace yqservice\yqserviceIntegration\responseObjects;

use yqservice\yqserviceIntegration\ResponseObject;

class CatalogOperationFieldObject extends ResponseObject
{

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $pattern;

    protected function fromXml($data)
    {
        $this->description = (string)$data['description'];
        $this->name        = (string)$data['name'];
        $this->pattern     = (string)$data['pattern'];
    }
}