<?php
require_once(__DIR__ . '/vendor/autoload.php');
use yqservice\controller\Controller;

$c   = 'YQSC1';
$step   = $_GET['step'];
$ssd = $_GET['ssd'];
$login = 'yq304650';
$key = '5oCvOXd96HU';
$Controller = new Controller;
$requests = [
    //'appendGetCatalogInfo' => [],
    'appendGetWizard2'     => [
        'ssd' => $ssd
    ]
];

$params = ['c' => $c, 'ssd' => $ssd, ''];
$data = $Controller->getData($requests, $params, $login, $key);
//echo "<pre>";
//print_r($data[0]->steps[$step]);die;
print_r(json_encode($data));die;
