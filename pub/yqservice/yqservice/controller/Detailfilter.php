<?php
/**
 * Created by YQService.
 * User: YQService
 * Date: 17.08.17
 * Time: 16:03
 */

namespace yqservice\controller;

use yqservice\Config;
use yqservice\yqserviceIntegration\responseObjects\FilterObject;
use yqservice\yqserviceIntegration\responseObjects\UnitObject;
use yqservice\controller\Controller;

/**
 * @property FilterObject filters
 * @property string domain
 * @property UnitObject detail
 * @property string oem
 * @property string brand
 * @property array from
 * @property string fromTask
 * @property string fromCatalogTask
 * @property string linkTarget
 */
class Detailfilter extends Controller
{
    public function Display($tpl = 'detailfilter', $view = 'view')
    {
        $catalogCode = $this->input->getString('c');
        $ssd         = $this->input->getString('ssd', '');
        $f           = $this->input->getString('f');
        $vid         = $this->input->getString('vid');
        $uid         = $this->input->getString('uid');
        $did         = $this->input->getString('did');
        $params      = ['c' => $catalogCode, 'ssd' => $ssd, ''];

        $requests = [
            'appendGetFilterByDetail' => [
                'f'   => $f,
                'vid' => $vid,
                'uid' => $uid,
                'did' => $did
            ],
            'appendGetUnitInfo'       => [
                'uid' => $uid
            ]
        ];

        $data = $this->getData($requests, $params);

        if ($data) {
            $fromTask        = $this->input->getString('fromTask');
            $fromCatalogTask = $this->input->getString('fromCatalogTask');
            $filters         = $data[0];
            $detail          = $data[1];

            $this->filters         = $filters;
            $this->domain          = Config::$useEnvParams ? $this->getBackUrl() : 'javascript:void(0)';
            $this->detail          = $detail;
            $this->linkTarget      = $this->getLinkTarget();
            $this->oem             = $this->input->getString('oem');
            $this->brand           = $this->input->getString('brand');
            $this->from            = $this->input->getArray();
            $this->fromTask        = $fromTask;
            $this->fromCatalogTask = $fromCatalogTask;
        }

        parent::Display($tpl, $view);
    }

}