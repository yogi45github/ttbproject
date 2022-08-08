<?php


namespace yqservice\yqserviceIntegration;

use yqservice\yqserviceIntegration\responseObjects\AttributeObject;
use SimpleXMLElement;

abstract class ResponseObject
{

    /**
     * @param SimpleXMLElement|string|array|null $data
     */
    public function __construct($data = null)
    {
        if (is_null($data)) {
        } else {
            if ($data instanceof SimpleXMLElement) {
                $this->fromXml($data);
            } else {
                $json = is_array($data) ? $data : json_decode($data, true);
                if ($json !== false) {
                    $this->fromJSON($json);
                }
            }
        }
    }

    /**
     * @param SimpleXMLElement $data
     */
    abstract protected function fromXml($data);

    protected function fromJSON($data)
    {
    }

    function get($column)
    {
        return property_exists($this, $column) ? $this->$column : null;
    }
}