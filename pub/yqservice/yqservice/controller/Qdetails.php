<?php
/**
 * Created by YQService.
 * User: YQService
 * Date: 16.08.17
 * Time: 14:25
 */

namespace yqservice\controller;

use yqservice\Config;
use yqservice\yqserviceIntegration\responseObjects\QuickDetailsObject;
use yqservice\yqserviceIntegration\responseObjects\VehicleObject;
use yqservice\modules\pathway\Pathway;
use yqservice\yqserviceIntegration\Language;
use yqservice\controller\Controller;

/**
 * @property array pathway
 * @property string gid
 * @property array categories
 * @property QuickDetailsObject details
 * @property VehicleObject vehicle
 * @property string format
 * @property string noimage
 * @property string domain
 * @property bool oem
 * @property bool showOems
 * @property string linkTarget
 */
class Qdetails extends Controller
{
    public function Display($tpl = 'qdetails', $view = 'view')
    {
        $catalogCode = $this->input->getString('c');
        $ssd         = $this->input->getString('ssd', '');
        $format      = $this->input->getString('format');
        $vid         = $this->input->getString('vid');
        $cid         = $this->input->getString('cid', -1);
        $gid         = $this->input->getString('gid');
        $oem         = $this->input->getString('oem');
        $params      = ['c' => $catalogCode, 'ssd' => $ssd, ''];

        $requests = [
            'appendGetCatalogInfo' => [],
            'appendGetVehicleInfo' => [
                'vid' => $vid
            ]
        ];

        if (!$oem) {
            $requests['appendListQuickDetail'] = [
                'vid' => $vid,
                'gid' => $gid,
                'all' => 1
            ];
        } else {
            $requests['appendGetOemPartApplicability'] = [
                'oem' => $oem,
            ];

            $this->applicability = true;
        }

        $data = $this->getData($requests, $params);

        $language = new Language();

        if ($data) {
            $vehicle     = $data[1];
            $details     = $data[2];
            $catalogInfo = $data[0];

            $pathway = new Pathway();

            $pathway->addItem($catalogInfo->name, $catalogInfo->link);

            $pathway->addItem($vehicle->name, $language->createUrl('vehicle', '', '', [
                'c'   => $catalogInfo->code,
                'vid' => $vehicle->vehicleid,
                'ssd' => $vehicle->ssd
            ]));
            //$pathway->addItem($vehicle->name, $language->createUrl('vehicle', '', '', ['c' => $this->input->getString('c'), 'vid' => $this->input->getString('vid', ''), 'ssd'=> $ssd]) : null);

            $pathway->addItem($language->t('detailsInGroup'));

            $this->pathway = $pathway->getPathway();
            $this->gid     = $this->input->getString('gid', '');
//            $this->categories = $categories;
            $this->details    = $details;
            $this->vehicle    = $vehicle;
            $this->format     = $format;
            $this->noimage    = Config::$imagePlaceholder;
            $this->oem        = $oem;
            $this->showOems   = Config::$showOemsToGuest;
            $this->domain     = $this->getBackUrl();
            $this->linkTarget = $this->getLinkTarget();
        }

        parent::Display($tpl, $view);
    }

}