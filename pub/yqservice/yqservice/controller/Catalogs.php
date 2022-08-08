<?php

namespace yqservice\controller;

use yqservice\Config;
use yqservice\yqserviceIntegration\Language;
use yqservice\modules\pathway\Pathway;
use yqservice\controller\Controller;

/**
 * @property string frameExample
 * @property string vinExample
 * @property array catalogs
 * @property int columns
 * @property float elemInRow
 * @property int elemCount
 * @property int rest
 * @property string vinFrameExample
 * @property bool letters
 * @property bool|string rev
 * @property bool|string revDesc
 * @property string oemExample
 * @property bool applicability
 * @property \DateTime revDate
 */
class Catalogs extends Controller
{

    public function Display($tpl = 'catalogs', $view = 'view')
    {

        $requests = [
            'appendListCatalogs' => []
        ];

        $data = $this->getData($requests);
        if ($data) {
            $dataObj  = $data[0]->catalogs;
            $examples = $data[0]->examples;

            $columns   = property_exists(new Config(), 'catalogColumns') ? Config::$catalogColumns : 3;
            $elemCount = count($dataObj ?: []);
            $elemInRow = floor(($elemCount) / $columns);
            $rest      = $elemCount % $columns;
            $rawRev    = false;
            $desc      = false;
            $revDate   = null;

            if (Config::$dev) {
                $hg      = shell_exec('hg log --verbose');
                $descPos = strpos($hg, 'description');
                $desc    = substr($hg, $descPos, 45);
                $rawRev  = substr($hg, 0, 16);
                $revDate = new \DateTime(shell_exec('hg log -l 1 --template "{date|localdate|rfc822date}"'));
            }

            $this->frameExample    = $examples[1];
            $this->vinExample      = $examples[0];
            $this->catalogs        = $dataObj;
            $this->columns         = $columns;
            $this->elemInRow       = $elemInRow;
            $this->elemCount       = $elemCount;
            $this->rest            = $rest;
            $this->vinFrameExample = $examples[rand(0, 1)];
            $this->oemExample      = !empty(Config::$oemExample) ? Config::$oemExample : '0913128000';
            $this->letters         = Config::$showCatalogsLetters;
            $this->applicability   = Config::$showApplicability;

            if (Config::$dev) {
                $this->rev     = $rawRev;
                $this->revDesc = $desc;
                $this->revDate = $revDate;
            }
        }

        parent::Display($tpl, $view);
    }
}




