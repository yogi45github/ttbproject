<?php
require_once(__DIR__ . '/vendor/autoload.php');
use yqservice\controller\Controller;

$username = "yq304650";
$password = "5oCvOXd96HU";

$Controller = new Controller;
$requests =[
	'appendFindVehicle' => [ 'ident' => 'ZCFC35A8005784708'],
    'appendGetCatalogInfo' => [ 'c' => 'YQIO1' ]
];
$params = ['c' => 'YQIO1'];

$data = $Controller->getData($requests, $perm, $username, $password);


echo "<pre>"; print_r($data);die;