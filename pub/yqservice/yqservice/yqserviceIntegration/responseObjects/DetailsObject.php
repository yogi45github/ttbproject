<?php
/**
 * Created by YQService
 * User: altunint
 * Date: 4/5/18
 * Time: 11:45 AM
 * TasK:
 */

namespace yqservice\yqserviceIntegration\responseObjects;

use yqservice\yqserviceIntegration\ResponseObject;
use yqservice\yqserviceIntegration\Factory;

class DetailsObject extends ResponseObject
{

    /**
     * @var DetailObject[]
     */
    public $details;

    protected function fromXml($data)
    {
        foreach ($data->row as $detail) {
            $this->details[] = new DetailObject($detail);
        }
    }

    public function toGroupsByCodeOnImage() {
        if (empty($this->details)) {
            return [];
        }
        $groups = [];
        foreach ($this->details as $detail) {
            if ((string) $detail->codeonimage) {
                if ($detail->codeonimage !== '-') {
                    $groups['i' . $detail->codeonimage][] = $detail;
                } else {
                    $groups['-'][] = $detail;
                }
            } else {
                $groups[] = $detail;
            }
        }
        return $groups;
    }
}