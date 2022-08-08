<?php
/**
 * Created by YQService
 * User: altunint
 * Date: 4/13/18
 * Time: 3:08 PM
 * TasK:
 */

namespace yqservice\yqserviceIntegration\responseObjects;

use yqservice\yqserviceIntegration\ResponseObject;

class QuickDetailsObject extends ResponseObject
{
    public $categories;

    protected function fromXml($data)
    {
        foreach ($data->Category as $category) {
            $this->categories[] = new CategoryObject($category);
        }
    }
}