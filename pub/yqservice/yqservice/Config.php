<?php

namespace yqservice;

if (Config::$dev) {
    // ini_set('display_errors', 1);
    // error_reporting(E_ALL);
}

class Config
{
    const baseDir   = __DIR__;
    const imageSize = 250;
    public static $dev = true;
    public static $ui_localization = 'en';
    public static $catalog_data = 'sv_SE';
    public static $useLoginAuthorizationMethod = true;
    public static $vehiclesMaxField = 20;
    public static $defaultUserLogin = '';
    public static $defaultUserKey = '';

    public static $oemServiceUrl = 'oem-api.yqservice.eu';

    public static $amServiceUrl = 'am-api.yqservice.eu';

    public static $useEnvParams = false;

    public static $showWelcomePage = true;

    public static $showToGuest = true;

    public static $showRequest = true;

    public static $showGroupsToGuest = true;

    public static $showOemsToGuest = true;

    public static $showApplicability = true;

    public static $SiteDomain = 'index.php?task=aftermarket&oem={article}&brand={brand}&options%5B%5D=crosses&options%5B%5D=weights&options%5B%5D=names&options%5B%5D=properties&options%5B%5D=images';

    public static $imagePlaceholder = 'yqservice/assets/images/no-image.gif';

    public static $catalogColumns = 4;

    public static $defaultVin = 'KMHVD34N8VU263043';

    public static $defaultFrame = 'XZU423-0001026';

    public static $showCatalogsLetters = true;

    public static $showFindPlate = true;

    public static $plateCountryCodes = ['SK', 'SE'];

    public static $disableAM = false;

    public static $showListPartsIcon = true;

    public static $theme = 'yqservice';
    public static $backurlError = 'index.php?task=error&type=backurl';
    public static $linkTarget = '_parent';

    public static $VehiclesColumns = [
        'brand',
        'name',
        'date',
        'datefrom',
        'dateto',
        'model',
        'framecolor',
        'trimcolor',
        'modification',
        'grade',
        'frame',
        'engine',
        'engineno',
        'transmission',
        'doors',
        'manufactured',
        'options',
        'creationregion',
        'destinationregion',
        'description'
    ];

    public static $groupVehicles = true;
    public static $toolbarPages = [
        'aftermarket',
        'catalog',
        'catalogs',
        'error',
        'qgroups',
        'vehicle',
        'vehicles',
        'wizard2',
        'qdetails',
        'unit',
        'applicabilitydetails'
    ];
}
