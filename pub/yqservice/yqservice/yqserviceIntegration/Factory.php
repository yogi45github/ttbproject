<?php

namespace yqservice\yqserviceIntegration;

use yqservice\yqserviceIntegration\responseObjects\AMDetailsObject;
use yqservice\yqserviceIntegration\responseObjects\CatalogsObject;
use yqservice\yqserviceIntegration\responseObjects\CatalogObject;
use yqservice\yqserviceIntegration\responseObjects\CategoriesObject;
use yqservice\yqserviceIntegration\responseObjects\CategoryObject;
use yqservice\yqserviceIntegration\responseObjects\DetailsObject;
use yqservice\yqserviceIntegration\responseObjects\DetailReferencesObject;
use yqservice\yqserviceIntegration\responseObjects\FilterObject;
use yqservice\yqserviceIntegration\responseObjects\GroupObject;
use yqservice\yqserviceIntegration\responseObjects\ImageMapObject;
use yqservice\yqserviceIntegration\responseObjects\QuickDetailsObject;
use yqservice\yqserviceIntegration\responseObjects\UnitsObject;
use yqservice\yqserviceIntegration\responseObjects\UnitObject;
use yqservice\yqserviceIntegration\responseObjects\VehiclesObject;
use yqservice\yqserviceIntegration\responseObjects\VehicleObject;
use yqservice\yqserviceIntegration\responseObjects\WizardObject;
use yqservice\yqserviceIntegration\responseObjects\PartsObject;
use SimpleXMLElement;

class Factory
{
    static $supportedTypes = [
        'QuickGroups',
        'DetailList',
        'ImageMap',
        'CatalogList',
        'Catalog',
        'Wizard',
        'VehicleList',
        'Vehicle',
        'QuickDetails',
        'CategoryList',
        'Category',
        'UnitList',
        'Unit',
        'Filter',
        'PartsList',
        'AftermarketDetailsList',
        'DetailReferencesList'
    ];

    public static function getObject($name, $data = null)
    {
        switch ((string)$name) {
            case 'QuickGroups':
                return new GroupObject($data);
                break;
            case 'DetailList':
                return new DetailsObject($data);
                break;
            case 'ImageMap':
                return new ImageMapObject($data);
                break;
            case 'CatalogList':
                return new CatalogsObject($data);
                break;
            case 'Catalog':
                return $data instanceof SimpleXMLElement ? new CatalogObject($data->row) : new CatalogObject($data);
                break;
            case 'Wizard':
                return new WizardObject($data);
                break;
            case 'VehicleList':
                return new VehiclesObject($data);
                break;
            case 'Vehicle':
                return $data instanceof SimpleXMLElement ? new VehicleObject($data->row) : new VehicleObject($data);
                break;
            case 'CategoryList':
                return new CategoriesObject($data);
                break;
            case 'Category':
                return $data instanceof SimpleXMLElement ? new CategoryObject($data->row) : new CategoryObject($data);
                break;
            case 'QuickDetails':
                return new QuickDetailsObject($data);
                break;
            case 'UnitList':
                return new UnitsObject($data);
                break;
            case 'Unit':
                return $data instanceof SimpleXMLElement ? new UnitObject($data->row) : new UnitObject($data);
                break;
            case 'Filter':
                return new FilterObject($data);
                break;
            case 'PartsList':
                return new PartsObject($data);
                break;
            case 'AftermarketDetailsList':
                return new AMDetailsObject($data);
                break;
            case 'DetailReferencesList':
                return new DetailReferencesObject($data);
                break;
            default:
                return $data;
                break;
        }
    }
}