<?php
/**
 * Created by YQService.
 * User: YQService
 * Date: 07.10.17
 * Time: 9:36
 */

namespace yqservice\controller;


use yqservice\Config;
use yqservice\yqserviceIntegration\YqserviceAftermarket;
use yqservice\yqserviceIntegration\Language;
use yqservice\modules\pathway\Pathway;
use yqservice\controller\Controller;

/**
 * @property array originalsList
 * @property array originalAliases
 */
class Aftermarket extends Controller
{
    public function Display($tpl = 'aftermarket', $view = 'view')
    {
        $language = new Language();

        if (isset(Config::$disableAM)) {
            if (Config::$disableAM) {
                $this->redirect($language->createUrl('catalogs', 'show'));

                die();
            }
        }

        if (!$this->isAuthoriseInAm()) {
            $this->renderAuthPage();
        }

        $view = $this->input->getString('view', 'view');
        $this->input->getString('view');
        $format = $this->input->getString('format');
        if (Config::$useEnvParams) {
            $this->redirect($language->createUrl('catalogs'));
        }

        switch ($view) {
            case 'view':
                $this->displayAftermarket();
                parent::Display($tpl, $view);

                break;
            case 'manufacturerinfo':
                $this->displayManufacturerInfo();
                break;
            case 'findOem':
                if ($format === 'raw') {
                    $this->displayFindOem();
                    parent::Display($tpl, 'view');
                } else {
                    $this->displayAftermarket();
                    parent::Display($tpl, 'view');
                }

                break;
        }

    }

    public function displayAftermarket()
    {
        $oem              = $this->input->getString('oem');
        $brand            = $this->input->getString('brand');
        $detailId         = $this->input->getString('detail_id');
        $input            = $this->input->getArray();
        $options          = isset($input['options']) ? $input['options'] : '';
        $replacementtypes = isset($input['replacementtypes']) ? $input['replacementtypes'] : [];
        $data             = [];

        if ($oem || $brand || $detailId) {
            $this->originalAliases = $this->getOriginalAliases();
            if ($detailId) {
                $request = [
                    'appendFindDetail' => [
                        'detail_id' => $detailId,
                        'options'   => $options
                    ]
                ];
            } else {
                if ($options) {
                    $options = implode(',', $options);
                } else {
                    $options = '';
                }

                $request = [
                    'appendFindOEM' => [
                        'oem'              => $oem,
                        'options'          => $options,
                        'brand'            => $brand,
                        'replacementtypes' => implode(',', $replacementtypes),
                    ]
                ];
            }

            $data = $this->getAftermarketData($request);
        }

        if (!$replacementtypes) {
            $replacementtypes = ['Default'];
        }

        $pathway = new Pathway();

        $pathway->addItem('AfterMarket', '');

        $this->pathway = $pathway->getPathway();

        $this->oem              = $oem;
        $this->brand            = $brand;
        $this->options          = $options;
        $this->replacementtypes = $replacementtypes;
        $this->details          = $data;

    }

    public function getOriginalAliases()
    {
        $request = [
            'appendListManufacturer' => []
        ];

        $originalList = array_filter(json_decode(json_encode($this->getAftermarketData($request)))->ListManufacturer->row, function ($item) {
            return !empty($item->{'@attributes'}->isoriginal) && $item->{'@attributes'}->isoriginal === 'true';
        });

        $aliasesArr = [];

        foreach ($originalList as $item) {
            $aliasesArr[$item->{'@attributes'}->name][] = $item->{'@attributes'}->name;
            if (!empty($item->{'@attributes'}->alias)) {
                $itemAliases = explode(',', $item->{'@attributes'}->alias);
                foreach ($itemAliases as $alias) {
                    $aliasesArr[$item->{'@attributes'}->name][] = $alias;
                }
            }
        }

        return $aliasesArr;
    }

    public function displayManufacturerInfo()
    {

        $manufacturerid = $_GET['manufacturerid'];

        $request = new YqserviceAftermarket(Config::$catalog_data);
        if (Config::$useLoginAuthorizationMethod) {
            $request->setUserAuthorizationMethod(Config::$defaultUserLogin, Config::$defaultUserKey);
        }
        $request->appendManufacturerInfo($manufacturerid);
        $data = $request->query();

        if ($request->error != '') {
            echo $request->error;
        } else {
            $data = $data[0]->ManufacturerInfo->row;
        }

        $this->loadTwig('aftermarket/tmpl', 'manufacturerInfo.twig', [
            'manufacturerInfo' => $data
        ]);
    }

    public function displayFindOem()
    {
        $brand            = $this->input->getString('brand', null);
        $oem              = $this->input->getString('oem', '');
        $options          = $this->input->getString('options', '');
        $detailId         = $this->input->getString('detail_id');
        $replacementtypes = !empty($this->input->getArray()['replacementtypes']) ? $this->input->getArray()['replacementtypes'] : '';

        if ($replacementtypes) {
            $replacementtypes = implode($replacementtypes, ',');
        } else {
            $replacementtypes = 'Default';
        }

        if ($detailId) {
            $this->originalAliases = self::getOriginalAliases();
            $data                  = $this->getAftermarketData([
                'appendFindDetail' => [
                    'detail_id' => $detailId,
                    'options'   => $options
                ]
            ]);

            if (!$data) {
                $this->loadTwig('error/tmpl', 'default.twig',
                    ['message' => $this->error]);
                $this->loadTwig('aftermarket/tmpl', 'view.twig', []);
            } else {
                if ($data) {

                    $this->loadTwig('aftermarket/tmpl', 'findOem.twig', [
                        'details' => $data
                    ]);
                }
            }

        } else {
            if ($options) {
                $options = implode(',', $options);
            } else {
                $options = '';
            }

            $data = $this->getAftermarketData([
                'appendFindOEM' => [
                    'oem'              => $oem,
                    'options'          => $options,
                    'brand'            => $brand,
                    'replacementtypes' => $replacementtypes
                ]
            ]);

            if ($this->request->error) {
                echo $this->error;
            } else {
                if (!$data) {
                    $data = $this->getAftermarketData([
                        'appendFindOEMCorrection' => [
                            'oem'              => $oem,
                            'options'          => $options,
                            'brand'            => $brand,
                            'replacementtypes' => $replacementtypes
                        ]
                    ]);
                }

                if ($data) {
                    $this->details = $data;
                }
            }
        }
    }
}