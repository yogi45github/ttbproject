<?php

namespace yqservice\yqserviceIntegration\responseObjects;

use yqservice\yqserviceIntegration\ResponseObject;
use yqservice\Config;
use yqservice\yqserviceIntegration\Language;

class VehiclesObject extends ResponseObject
{

    protected static $mainAttributes = [
        'brand',
        'name',
        'grade',
        'transmission',
        'doors',
        'creationregion',
        'destinationregion',
        'date',
        'manufactured',
        'framecolor',
        'trimcolor',
        'datefrom',
        'dateto',
        'frame',
        'frames',
        'framefrom',
        'frameto',
        'engine',
        'engine1',
        'engine',
        'engineno',
        'options',
        'modelyearfrom',
        'modelyearto',
        'modification',
        'description',
    ];

    /**
     * @var VehicleObject[]
     */
    public $vehicles;

    /**
     * @var array
     */
    public $tableHeaders;

    /**
     * @var array
     */
    public $tableColumns;

    /**
     * @var array
     */
    public $commonColumns;

    /**
     * @var array
     */
    public $groupedByName;


    /**
     * @param \SimpleXMLElement $data
     */
    protected function fromXml($data)
    {
        foreach ($data->row as $vehicle) {
            $this->vehicles[] = new VehicleObject($vehicle);
        }
    }

    protected function fromJSON($data)
    {
        if (!empty($data['IdentifiedVehiclesResponse']) && !empty($data['IdentifiedVehiclesResponse']['vehicles'])) {
            foreach ($data['IdentifiedVehiclesResponse']['vehicles'] as $vehicle) {
                $this->vehicles[] = new VehicleObject($vehicle);
            }
        }
    }

    public function groupColumnsByVehicles()
    {
        $columns      = self::$mainAttributes;
        $columnValues = [];
        if ($this->vehicles) {
            foreach ($this->vehicles as $vehicle) {
                foreach ($columns as $column) {
                    if ($vehicle->$column) {
                        $columnValues[$column][$vehicle->$column] = isset($columnValues[$column][$vehicle->$column]) ? $columnValues[$column][$vehicle->$column] + 1 : 1;
                        $this->tableHeaders[$column]              = $column;
                    }
                }
                if ($vehicle->attributes) {
                    foreach ($vehicle->attributes as $column => $attribute) {
                        $columnValues[$column][$attribute->value] = isset($columnValues[$column][$attribute->value]) ? $columnValues[$column][$attribute->value] + 1 : 1;
                        $this->tableHeaders[$column]              = $attribute->name;
                    }
                }
            }
        }

        foreach ($columnValues as $column => $values) {
            if (count($values) > 1) {
                $this->tableColumns[] = $column;
            } else {
                foreach ($values as $value => $count) {
                    if ($count == count($this->vehicles)) {
                        $attributeObject = new AttributeObject([
                            'key'   => $column,
                            'name'  => $this->tableHeaders[$column],
                            'value' => $value
                        ]);

                        $this->commonColumns[] = $attributeObject;
                    } else {
                        $this->tableColumns[] = $column;
                    }
                }
            }

        }

        return $this;
    }

    public function groupVehiclesByName()
    {
        $vehicles = $this->vehicles;
        if (!$vehicles) {
            return false;
        }

        foreach ($vehicles as $vehicle) {
            if (!isset($this->groupedByName[$vehicle->name])) {
                $this->groupedByName[$vehicle->name] = $vehicle;
            } else {
                if ($this->groupedByName[$vehicle->name] !== $vehicle) {
                    $this->groupedByName[$vehicle->name]->children[] = $vehicle;
                }
            }
        }

        $this->groupColumnsByVehicles();

        return $this;
    }
}