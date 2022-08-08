<?php
	ini_set ('display_errors',1);
	ini_set ('memory_limit',-1);
	ini_set('max_execution_time', 36000);
	use Magento\Framework\App\Bootstrap;
	require 'app/bootstrap.php';
	$bootstrap = Bootstrap::create(BP, $_SERVER);
	$objectManager = $bootstrap->getObjectManager();
	$state = $objectManager->get('Magento\Framework\App\State');
	$state->setAreaCode('frontend');

	$csvData = fopen('set_customer_attribute_value.csv', 'r');
	$customerData = $objectManager->create('Magento\Customer\Model\Customer');
	while (($data = fgetcsv($csvData)) !== FALSE) {
		$customerData->setWebsiteId(1);
		$customerData->loadByEmail($data[0]);
		if(!empty($data[1])){
			$customerData->setTtbId($data[1]);
		}
		if(!empty($data[2])){
			$customerData->setOrganisationNumber($data[2]);
		}
		$customerData->save();
		echo $data[0]."\n";
	}
?>