<?php
/**
 * Created by PhpStorm.
 * User: applebred
 * Date: 16.01.19
 * Time: 15:39
 */

namespace yqservice\yqserviceIntegration\responseObjects;


use yqservice\yqserviceIntegration\ResponseObject;
use SimpleXMLElement;

class DetailReferencesObject extends ResponseObject
{
    /**
     * @var string $oem
     */
    public $oem;

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var array $referencesList
     */
    public $referencesList;

    /**
     * @param SimpleXMLElement $data
     */
    protected function fromXml($data)
    {
        if (!empty($data)) {
            $this->oem = (string)$data->OEMPartReference->attributes()->oem;
            $this->name = (string)$data->OEMPartReference->name;
            $this->referencesList = [];

            if (!empty($data->OEMPartReference)) {
                foreach ($data->OEMPartReference as $OEMPartReferenceItem) {
                    foreach ($OEMPartReferenceItem->CatalogReferences as $catalogReference) {
                        foreach ($catalogReference->CatalogReference as $reference) {
                            $this->addNewReference($OEMPartReferenceItem, $reference);
                        }
                    }
                }
            }
        }
    }

    private function addNewReference($OEMPartReferenceItem, $catalogReference) {
        $creationalData = [
            'detail'  => $OEMPartReferenceItem,
            'catalog' => $catalogReference
        ];

        $detailReference = new DetailReferenceObject($creationalData);
        $this->referencesList[] = $detailReference;
    }
}