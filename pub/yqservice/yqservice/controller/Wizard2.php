<?php

namespace yqservice\controller;

use yqservice\yqserviceIntegration\Language;
use yqservice\modules\pathway\Pathway;
use yqservice\controller\Controller;


class Wizard2 extends Controller
{

    public function Display($tpl = 'wizard2', $view = 'view')
    {
        $c   = $this->input->getString('c', '');
        $ssd = $this->input->getString('ssd', '');

        $language = new Language();

        $requests = [
            'appendGetCatalogInfo' => [],
            'appendGetWizard2'     => [
                'ssd' => $ssd
            ]
        ];

        $params = ['c' => $c, 'ssd' => $ssd, ''];

        $data = $this->getData($requests, $params);

        if ($data) {
            $wizard      = $data[1]->steps;
            $catalogInfo = $data[0];
            $catalogLink = $catalogInfo->link;

            $pathway = new Pathway();

            $pathway->addItem($catalogInfo->name, $catalogLink);

            $pathway->addItem($language->t('findByWizard2'));

            $this->pathway     = $pathway->getPathway();
            $this->ssd         = $ssd;
            $this->wizard      = $wizard;
            $this->cataloginfo = $catalogInfo;
            $this->c           = $catalogInfo->code;
        }

        parent::Display($tpl, $view);
    }
}
