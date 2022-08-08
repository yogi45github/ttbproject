<?php

namespace yqservice;

use yqservice\controller\Controller;
define('YQSERVICE_DIR', __DIR__);

$login = 'yq304650';
$key = '5oCvOXd96HU';
$Controller = new Controller;
$requests = [
    'appendListCatalogs' => []
];
$data = $Controller->getData($requests, [], $login, $key);
$_SESSION['logged']   = true;
$_SESSION['username'] = $login;
$_SESSION['key']      = $key;
app::start();