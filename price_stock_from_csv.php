<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use Magento\Framework\App\Bootstrap;
require 'app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$objectManager->get('\Magento\Framework\App\State')->setAreaCode('frontend'); // for remove Area code is not set error
$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();
$Table = $resource->getTableName('partly_product');
$filename = 'Final_price_sheet_truck.csv';
$row = 0;
$handle = fopen($filename,"r");
$truncatre_sql = "TRUNCATE TABLE ".$Table;
$connection->query($truncatre_sql);
while( $data = fgetcsv( $handle) ) {
	$product = $data[0];
	$alternate = $data[1];
	$oem = addslashes($data[2]);
	$brand = $data[3];
	$price = $data[4];
	$stock = $data[5];
	$sql = "INSERT INTO " . $Table . "(name,alternate, oem, brand,price,stock) VALUES ('".$product."','".$alternate."','".$oem."','".$brand."','".$price."','".$stock."')";
    $connection->query($sql);
    echo $product."\n";
}
echo "Data Inserted Successfully!";