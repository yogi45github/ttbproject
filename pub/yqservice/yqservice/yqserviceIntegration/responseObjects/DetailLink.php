<?php
/**
 * Created by YQService.
 * User: YQService
 * Date: 14.03.19
 * Time: 14:54
 */

namespace yqservice\yqserviceIntegration\responseObjects;


use yqservice\yqserviceIntegration\ResponseObject;
use yqservice\yqserviceIntegration\Language;
use SimpleXMLElement;

class DetailLink extends ResponseObject
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $command;

    /**
     * @var array
     */
    public $attributes;

    public function getLink()
    {
        $language = new Language();
        $link     = null;

        switch ($this->command) {
            case 'LISTUNITS':
                $params = [
                    'c'              => $this->attributes['Catalog'],
                    'cid'            => $this->attributes['CategoryId'],
                    'vid'            => $this->attributes['VehicleId'],
                    'ssd'            => $this->attributes['ssd'],
                    'linkedWithUnit' => 1
                ];

                $link = $language->createUrl('vehicle', 'view', '', $params);
                break;
        }

        return $link;
    }

    /**
     * @param SimpleXMLElement $data
     */
    protected function fromXml($data)
    {
        $serviceData = json_decode(json_encode($data));
        foreach ($serviceData->Link->{'@attributes'} as $key => $attribute) {
            if (strtolower($key) === 'type') {
                $this->type = $attribute;
            } elseif (strtolower($key) === 'label') {
                $this->label = $attribute;
            } elseif (strtolower($key) === 'command') {
                $this->command = $attribute;
            } else {
                $this->attributes[$key] = $attribute;
            }
        }
    }
}