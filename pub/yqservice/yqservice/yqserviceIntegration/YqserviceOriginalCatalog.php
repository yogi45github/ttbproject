<?php
namespace yqservice\yqserviceIntegration;

use yqservice\Config;
use yqservice\yqserviceIntegration\Yqservice;
use yqservice\yqserviceIntegration\Factory;


class YqserviceOriginalCatalog
{
    /** @var Yqservice */
    public $soap;
    public $error;
    public $errorTrace;
    public $data;

    public $responseData;

    //	Results
    protected $locale;
    protected $catalog;
    protected $ssd;
    protected $resultObjectNames = [];
    protected $queries = [];

    function __construct($catalog = '', $ssd = '', $locale = 'en_US')
    {
        $language = new Language();

        $locale = $language->getLocalization();

        if (getenv('UUE_LOCALE')) {
            $locale = $this->checkParam(base64_decode(getenv('UUE_LOCALE')));
        }

        if (!$locale) {
            $locale = Config::$catalog_data;
        }

        $this->locale  = $this->checkParam($locale);
        $this->catalog = $this->checkParam($catalog);
        $this->ssd     = $this->checkParam($ssd);
        $this->soap    = new Yqservice();
        $this->soap->setCertificateAuthorizationMethod();
    }

    function checkParam($value)
    {
        return $value;
    }

    public function setUserAuthorizationMethod($login, $key)
    {
        $this->soap->setUserAuthorizationMethod($login, $key);
    }

    function appendGetCatalogInfo()
    {
        $this->resultObjectNames[] = 'Catalog';
        $this->appendCommand('GetCatalogInfo',
            ['Locale' => $this->locale, 'Catalog' => $this->catalog, 'ssd' => $this->ssd]);
    }

    function appendCommand($command, $params)
    {
        $item          = new \stdClass();
        $item->command = $command;
        $item->params  = $params;
        if (isset($params) && is_array($params)) {
            $command .= ':';
            $first   = true;
            foreach ($params as $key => $value) {
                if ($first) {
                    $first = false;
                } else {
                    $command .= '|';
                }
                $command .= $key . '=' . $value;
            }

            $item->command_text = $command;
        } else {
            $item->command_text = $command;
        }

        $this->queries[] = $item;
    }

    function appendFindVehicle($identString)
    {
        $this->resultObjectNames[] = 'VehicleList';
        $this->appendCommand('FindVehicle', ['Locale' => $this->locale, 'IdentString' => $identString]);
    }

    public function appendFindVehicleByPlateNumber($CountryCode, $PlateNumber)
    {
        $this->resultObjectNames[] = 'VehicleList';
        $this->appendCommand('FINDVEHICLEBYPLATENUMBER', ['Locale' => $this->locale, 'CountryCode' => $CountryCode, 'PlateNumber' => $PlateNumber]);
    }

    function appendListCatalogs()
    {
        $this->resultObjectNames[] = 'CatalogList';
        $this->appendCommand('ListCatalogs', ['Locale' => $this->locale, 'ssd' => $this->ssd]);
    }

    function appendFindVehicleByVIN($vin, $inCatalog = false)
    {
        $this->resultObjectNames[] = 'VehicleList';
        $this->appendCommand('FindVehicleByVIN', [
            'Locale'    => $this->locale,
            'Catalog'   => $inCatalog ? $this->catalog : '',
            'VIN'       => $this->checkParam($vin),
            'ssd'       => $this->ssd,
            'Localized' => 'true'
        ]);
    }

    function appendFindVehicleByFrameNo($frameNo, $inCatalog = false)
    {
        $this->resultObjectNames[] = 'VehicleList';
        $this->appendCommand('FindVehicleByFrameNo', [
            'Locale'    => $this->locale,
            'Catalog'   => $inCatalog ? $this->catalog : '',
            'FrameNo'   => $this->checkParam($frameNo),
            'ssd'       => $this->ssd ?: '',
            'Localized' => 'true'
        ]);
    }

    function appendFindVehicleByWizard2($ssd)
    {
        $this->resultObjectNames[] = 'VehicleList';
        $this->appendCommand('FindVehicleByWizard2', [
            'Locale'    => $this->locale,
            'Catalog'   => $this->catalog,
            'ssd'       => $this->checkParam($ssd),
            'Localized' => 'true'
        ]);
    }

    function appendGetVehicleInfo($vehicleid)
    {
        $this->resultObjectNames[] = 'Vehicle';
        $this->appendCommand('GetVehicleInfo', [
            'Locale'    => $this->locale,
            'Catalog'   => $this->catalog,
            'VehicleId' => $this->checkParam($vehicleid),
            'ssd'       => $this->ssd,
            'Localized' => 'true'
        ]);
    }

    function appendListCategories($vehicleid, $categoryid, $ssd = null)
    {
        $this->resultObjectNames[] = 'CategoryList';
        $this->appendCommand('ListCategories', [
            'Locale'     => $this->locale,
            'Catalog'    => $this->catalog,
            'VehicleId'  => $this->checkParam($vehicleid),
            'CategoryId' => $this->checkParam($categoryid),
            'ssd'        => $ssd ?: $this->ssd
        ]);
    }

    function appendListUnits($vehicleid, $categoryid, $ssd = null)
    {
        $this->resultObjectNames[] = 'UnitList';
        $this->appendCommand('ListUnits', [
            'Locale'     => $this->locale,
            'Catalog'    => $this->catalog,
            'VehicleId'  => $this->checkParam($vehicleid),
            'CategoryId' => $this->checkParam($categoryid),
            'ssd'        => $ssd ?: $this->ssd,
            'Localized'  => 'true'
        ]);
    }

    function appendGetUnitInfo($unitid)
    {
        $this->resultObjectNames[] = 'Unit';
        $this->appendCommand('GetUnitInfo', [
            'Locale'    => $this->locale,
            'Catalog'   => $this->catalog,
            'UnitId'    => $this->checkParam($unitid),
            'ssd'       => $this->ssd,
            'Localized' => 'true'
        ]);
    }

    function appendListImageMapByUnit($unitid)
    {
        $this->resultObjectNames[] = 'ImageMap';
        $this->appendCommand('ListImageMapByUnit', [
            'Catalog'   => $this->catalog,
            'UnitId'    => $this->checkParam($unitid),
            'ssd'       => $this->ssd,
            'WithLinks' => 'true'
        ]);
    }

    function appendListDetailByUnit($unitid)
    {
        $this->resultObjectNames[] = 'DetailList';
        $this->appendCommand('ListDetailByUnit', [
            'Locale'    => $this->locale,
            'Catalog'   => $this->catalog,
            'UnitId'    => $this->checkParam($unitid),
            'ssd'       => $this->ssd,
            'Localized' => 'true',
            'WithLinks' => 'true'
        ]);
    }

    function appendGetWizard2($ssd = false)
    {
        $this->resultObjectNames[] = 'Wizard';
        $this->appendCommand('GetWizard2',
            ['Locale' => $this->locale, 'Catalog' => $this->catalog, 'ssd' => $this->checkParam($ssd)]);
    }

    function appendGetFilterByUnit($filter, $vehicle_id, $unit_id)
    {
        $this->resultObjectNames[] = 'Filter';
        $this->appendCommand('GetFilterByUnit', [
            'Locale'    => $this->locale,
            'Catalog'   => $this->catalog,
            'Filter'    => $this->checkParam($filter),
            'VehicleId' => $this->checkParam($vehicle_id),
            'UnitId'    => $this->checkParam($unit_id),
            'ssd'       => $this->ssd
        ]);
    }

    function appendGetFilterByDetail($filter, $vehicle_id, $unit_id, $detail_id)
    {
        $this->resultObjectNames[] = 'Filter';
        $this->appendCommand('GetFilterByDetail', [
            'Locale'    => $this->locale,
            'Catalog'   => $this->catalog,
            'Filter'    => $this->checkParam($filter),
            'VehicleId' => $this->checkParam($vehicle_id),
            'UnitId'    => $this->checkParam($unit_id),
            'DetailId'  => $this->checkParam($detail_id),
            'ssd'       => $this->ssd
        ]);
    }

    function appendListQuickGroup($vehicle_id)
    {
        $this->resultObjectNames[] = 'QuickGroups';
        $this->appendCommand('ListQuickGroup', [
            'Locale'    => $this->locale,
            'Catalog'   => $this->catalog,
            'VehicleId' => $this->checkParam($vehicle_id),
            'ssd'       => $this->ssd
        ]);
    }

    function appendFindVehicleCustom($searchType, $searchParams)
    {
        $this->resultObjectNames[] = 'VehicleList';
        $params                    = [
            'Locale'  => $this->locale,
            'Catalog' => $this->catalog,
            'Code'    => $this->checkParam($searchType)
        ];
        $this->appendCommand('FindVehicleCustom',
            $searchParams && is_array($searchParams) ? array_merge($params, $searchParams) : $params);
    }

    function appendListQuickDetail($vehicle_id, $group_id, $all = 0)
    {
        $this->resultObjectNames[] = 'QuickDetails';
        $params                    = [
            'Locale'       => $this->locale,
            'Catalog'      => $this->catalog,
            'VehicleId'    => $this->checkParam($vehicle_id),
            'QuickGroupId' => $group_id,
            'ssd'          => $this->ssd,
            'Localized'    => 'true'
        ];

        if ($all) {
            $params['All'] = 1;
        }

        $this->appendCommand('ListQuickDetail', $params);
    }

    function appendFindDetailApplicability($oem, $brand = '')
    {
        $this->resultObjectNames[] = 'DetailApplicability';
        $this->appendCommand('FindDetailApplicability', [
            'Locale'    => $this->locale,
            'OEM'       => $this->checkParam($oem),
            'Brand'     => $brand,
            'Localized' => 'true'
        ]);
    }


    public function appendExecCustomOperation($operation, $data)
    {
        $this->resultObjectNames[] = 'VehicleList';
        if (!is_array($data)) {
            $data = [];
        }

        $this->appendCommand('ExecCustomOperation', array_merge([
            'Locale'    => $this->locale,
            'Catalog'   => $this->catalog,
            'operation' => $this->checkParam($operation)
        ], $data));
    }

    public function appendListOemParts($vid, $ssd = null, $catalog = null)
    {
        $this->resultObjectNames[] = 'PartsList';
        $this->appendCommand('ListOEMParts', [
            'ssd'       => $ssd ?: $this->ssd,
            'Catalog'   => $catalog ?: $this->catalog,
            'VehicleId' => $vid,
            'Locale'    => $this->locale
        ]);
    }

    public function appendFindApplicableVehicles($oem)
    {
        $this->resultObjectNames[] = 'VehicleList';
        $this->appendCommand('FindApplicableVehicles', [
            'OEM'     => $this->checkParam($oem),
            'Catalog' => $this->catalog,
            'ssd'     => $this->ssd,
            'Locale'  => $this->locale
        ]);
    }

    public function appendGetOemPartApplicability($oem)
    {
        $this->resultObjectNames[] = 'QuickDetails';
        $this->appendCommand('GetOEMPartApplicability', [
            'Catalog' => $this->catalog,
            'OEM'     => $oem,
            'ssd'     => $this->ssd,
            'Locale'  => $this->locale
        ]);
    }

    public function appendFindPartReferences($oem)
    {
        $this->resultObjectNames[] = 'DetailReferencesList';
        $this->appendCommand('FINDPARTREFERENCES', [
            'Locale' => $this->locale,
            'OEM'    => $oem
        ]);
    }

    function query()
    {
        $time_start = microtime(true);
        $result     = [];
        $request    = [];
        $count      = count($this->queries);

        for ($index = 0; $index < $count; $index++) {
            $result[$index]  = null;
            $request[$index] = $this->queries[$index];
        }

        $commands_index = 0;
        $query          = '';
        $indexes        = [];
        for ($index = 0; $index < $count; $index++) {
            if ($request[$index]) {
                if ($query) {
                    $query .= "\n";
                }
                $query                .= $request[$index]->command_text;
                $indexes[]            = $index;
                $this->textRequests[] = $request[$index]->command_text;

                if ($commands_index == 5) {
                    if (!$this->_query($query, $indexes, $result)) {
                        return false;
                    }

                    $commands_index = 0;
                    $query          = '';
                    $indexes        = [];
                }

                $commands_index++;
            }
        }

        if ($commands_index > 0) {
            (!$this->_query($query, $indexes, $result));
        }

        $this->data = $result;

        $this->queries = [];

        $resultObject = [];


        foreach ($this->data as $key => $chunk) {
            if (isset($this->resultObjectNames[$key])) {
                if (in_array($this->resultObjectNames[$key], Factory::$supportedTypes)) {
                    $resultObject[$key] = Factory::getObject($this->resultObjectNames[$key],
                        $chunk instanceof SimpleXMLElement ? $chunk->children() : $chunk);
                } else {
                    $resultObject[$key] = $chunk;
                }
            }
        }

        $time_end = microtime(true);

        $this->requestTime = $time_end - $time_start;

        return $resultObject;
    }

    function _query($query, $indexes, &$result)
    {
        try {
            $data               = $this->soap->queryData($query);
            $this->responseData = $data;
        } catch (\Exception $exc) {
            $this->error      = $exc->getMessage();
            $this->errorTrace = $exc->getTrace();
        }

        if ($this->soap->getError()) {
            $this->error      = $this->soap->getError();
            $this->errorTrace = $this->soap->getErrorTrace();

            return false;
        }

        $data  = simplexml_load_string($data);
        $index = 0;

        //  Merge results
        if ($data && method_exists(get_class($data), 'children')) {
            foreach ($data->children() as $row) {
                $result[$indexes[$index]] = $row;

                $index++;
            }
        }
    }
}