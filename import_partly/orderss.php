<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Magento\Framework\App\Bootstrap;
require 'app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$objectManager->get('\Magento\Framework\App\State')->setAreaCode('frontend'); // for remove Area code is not set error
$orderObj = $objectManager->create('Magento\Checkout\Model\Session')->getLastRealOrder();
$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();
$orderItems = $orderObj->getAllItems();
$itemQty = array();
foreach ($orderItems as $item) {
	$productid = $item->getProduct()->getId();
	if($productid == 29){
        $itemdescription = $item->getDescription();
        $data = json_decode($itemdescription);
        $article_no = $data->artical;
        $qtyordered = $item->getQtyOrdered();
        $sql = "UPDATE `partly_product` SET `stock` = `stock` - ".$qtyordered." WHERE `name` = '".$article_no."'";
        $connection->query($sql);
    }
}
?>