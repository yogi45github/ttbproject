<?php
require_once(__DIR__ . '/vendor/autoload.php');
use yqservice\controller\Controller;

$username = "yq304650";
$password = "5oCvOXd96HU";

$Controller = new Controller;
$requests = [
    'appendListCatalogs' => []
];
$data = $Controller->getData($requests, [], $username, $password);
$catalogs = $data[0]->catalogs;

$brands = [];
foreach ($catalogs as $key => $catalog) {
	$brands[$key]['brand'] = $catalog->brand;
	$brands[$key]['code'] = $catalog->code;
	$brands[$key]['name'] = $catalog->name;
}
echo "<pre>"; print_r($brands);die;