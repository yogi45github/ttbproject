<?php

namespace Truck\Parts\Controller\Detail;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Result\Page;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;

class Index extends \Magento\Framework\App\Action\Action
{   
    protected $Client;
    public function crossItems($partId){
        $client = new Client();
            $graphQLquery = '{"query": "{\n gapc {\n resolve_gapc_parts(id: \"'.$partId.'\") {\n interchange(page: 1, limit: 200, is_oem: false) {\n items {\n name\n barcode\n is_universal\n is_oe\n is_performance\n gtin14\n width_mm\n length_mm\n height_mm\n weight_g\n packing_unit\n quantity_per_packing_unit\n description\n mpn\n gapc_brand {\n id\n name\n }\n gapc_position {\n id\n name\n }\n gapc_attributes {\n id\n name\n value\n }\n }\n }\n \n} \n} \n} "}';

            $response = $client->request('post', 'https://api.partly.com/node-api/graphql', [
                'headers' => [
                    'X-Api-Key' => '2d5a3f39716389401f4256dd69d06836:146301f30cc5c88f3c2251cf95987ac5:db24a1dcbb6b1e3b5cefa9d4eeb07565930ceb149fbb3cffa312f38db9332afe4ad75dbdba1a13acce420d2ca5b49263ecd96f8eb7908eef464ea5140c1fd16e03a8eb8f45f96973533d3bc52d4867cb9677c21a112a716cc67c39550fce2fd0e6dd5fabcca98d4214315194616c3134d8cfbf71c900491a3f31bb4026b0f02c80fda12d5025a36c0ae656ff963ecabcb52d062e213902e847b7013e1ff31e61f55bbab78a0da0da119f',
                    'Content-Type' => 'application/json'
                ],
                'body' => $graphQLquery
            ]);
            $response->getStatusCode();
            $responseBody = $response->getBody();
            $responseContent = json_decode($responseBody->getContents());
            return $responseContent->data->gapc->resolve_gapc_parts[0]->interchange->items;
    }
    public function execute()
    {
        $partnumber = preg_replace("/[^a-zA-Z0-9]+/", "", $_GET['partnumber']);
        $inter = $_GET['inter'];
        $items = [];
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutSession  = $objectManager->get('\Magento\Catalog\Model\Session');
        $sessionPartnumber = $checkoutSession->getPartnumber();
        $sessionItems = $checkoutSession->getPartData();

        if($sessionPartnumber && $sessionItems && ($sessionPartnumber == $partnumber)){
            $partnumber = $sessionPartnumber;
            $items = $sessionItems;
        } else if($partnumber){
            $client = new Client();
            $graphQLquery = '{"query":"{\n gapc {\n search_gapc_parts(query: \"'.$partnumber.'\", limit: 500) {items {\n id\n name\n description\n fitted_uvdb_vehicle_definitions {\n items {\n name\n uvdb_make {\n name\n }\n uvdb_models {\n name\n }\n }\n }\n gapc_part_type {\n id\n name\n }\n interchange(limit: 500) {\n items {\n name\n barcode\n is_universal\n is_oe\n is_performance\n gtin14\n width_mm\n length_mm\n height_mm\n weight_g\n packing_unit\n quantity_per_packing_unit\n description\n mpn\n gapc_brand {\n id\n name\n }\n gapc_position {\n id\n name\n }\n gapc_attributes {\n id\n name\n value\n }\n }\n }\n }\n }\n }\n }"}';

            $response = $client->request('post', 'https://api.partly.com/node-api/graphql', [
                'headers' => [
                    'X-Api-Key' => '2d5a3f39716389401f4256dd69d06836:146301f30cc5c88f3c2251cf95987ac5:db24a1dcbb6b1e3b5cefa9d4eeb07565930ceb149fbb3cffa312f38db9332afe4ad75dbdba1a13acce420d2ca5b49263ecd96f8eb7908eef464ea5140c1fd16e03a8eb8f45f96973533d3bc52d4867cb9677c21a112a716cc67c39550fce2fd0e6dd5fabcca98d4214315194616c3134d8cfbf71c900491a3f31bb4026b0f02c80fda12d5025a36c0ae656ff963ecabcb52d062e213902e847b7013e1ff31e61f55bbab78a0da0da119f',
                    'Content-Type' => 'application/json'
                ],
                'body' => $graphQLquery
            ]);
            $response->getStatusCode();
            $responseBody = $response->getBody();
            $responseContent = json_decode($responseBody->getContents());
            $items = $responseContent->data->gapc->search_gapc_parts->items;
            $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();        
            $checkoutSession  = $objectManager->get('\Magento\Catalog\Model\Session');
            $checkoutSession->setPartnumber($partnumber);
            $checkoutSession->setPartData($items);
        }

        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $block = $page->getLayout()->getBlock('parts_detail_index');
        $block->setData('items', $items);
        $block->setData('partnumber', $partnumber);
        $block->setData('product', null);
        $block->setData('price', 0);
        $block->setData('stock', 0);
        foreach ($items as $key => $item) {
            $innerInterchangeItems = $item->interchange->items;
                if(count($innerInterchangeItems) > 0){
                    foreach($innerInterchangeItems as $innerInterchangeItem){
                        if($innerInterchangeItem->mpn == $inter){
                             $connection = $objectManager
                                ->get('Magento\Framework\App\ResourceConnection')
                                ->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 
                            $articalName = str_replace(" ","", $innerInterchangeItem->mpn);
                            $result1 = $connection->fetchAll("SELECT * FROM `partly_product` WHERE `name` = '".$articalName."' AND `brand` = '".$innerInterchangeItem->gapc_brand->name."' ORDER BY `id` DESC");
                            $block->setData('product', $innerInterchangeItem);
                            $block->setData('partid', $item->id);
                            $block->setData('crossItems', $this->crossItems($item->id));
                            $block->setData('price', $result1[0]['price']);
                            $block->setData('stock', $result1[0]['stock']);
                            $checkoutSession->setCustomProductPrice($result1[0]['price']);
                            $checkoutSession->setCustomProductName($innerInterchangeItem->name);
                            $checkoutSession->setCustomProductArticalName($articalName);
                            $checkoutSession->setCustomProductBrand($innerInterchangeItem->gapc_brand->name);
                            $checkoutSession->setCustomProductPartnumber($partnumber);
                        }
                    }
                }
        }
        return $page;
    }
}