<?php

namespace Truck\Parts\Controller\Index;

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

    public function execute() {
        // $partnumber = preg_replace("/[^a-zA-Z0-9]+/", "", $_GET['partnumber']);
        $partnumber =$_GET['partnumber'];
        if (strpos($partnumber, '`') !== false){
            $partnumber = str_replace("`","#", $partnumber);
        }
        if (isset($_GET['replacedoem'])) {
            $replacedoem = preg_replace("/[^a-zA-Z0-9]+/", "", $_GET['replacedoem']);
        }
        // $replacedoem = $_GET['replacedoem'];
        $items = [];
        if($partnumber){
            $items = $this->getPartlyData($partnumber);
            // if(count($items) <= 0  && $replacedoem){
            //     $items = $this->getPartlyData($partnumber);
            // }
        }
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $block = $page->getLayout()->getBlock('parts_index_index');
        $block->setData('items', $items);
        $block->setData('partnumber', $partnumber);
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();        
        $checkoutSession  = $objectManager->get('\Magento\Catalog\Model\Session');
        $checkoutSession->setPartnumber($partnumber);
        $checkoutSession->setPartData($items);
        return $page;
    }

    public function getPartlyData($partnumber) {
        $client = new Client();
            //$graphQLquery = '{"query":"{\n gapc {\n search_gapc_parts(query: \"'.$partnumber.'\", limit: 500) {items {\n id\n name\n description\n fitted_uvdb_vehicle_definitions {\n items {\n name\n uvdb_make {\n name\n }\n uvdb_models {\n name\n }\n }\n }\n gapc_part_type {\n id\n name\n }\n interchange(limit: 500) {\n items {\n name\n barcode\n is_universal\n is_oe\n is_performance\n gtin14\n width_mm\n length_mm\n height_mm\n weight_g\n packing_unit\n quantity_per_packing_unit\n description\n mpn\n gapc_brand {\n id\n name\n }\n gapc_position {\n id\n name\n }\n gapc_images {\n large_url\n }\n gapc_attributes {\n id\n name\n value\n }\n }\n }\n }\n }\n }\n }"}';

            //$graphQLquery = '{"query":"{\n gapc {\n search_gapc_parts(part_number: \"'.$partnumber.'\"\n search_context: { use_fuzzy_search: false, search_interchangeable: false }\n ) {\n items {\n id\n name\n description\n length_mm\n weight_g\n width_mm\n height_mm\n products {\n items {\n store {\n name\n }\n sku\n images {\n large_url\n }\n }\n }\n name\n part_number\n gapc_attributes {\n name\n id\n value\n }\n gapc_images {\n large_url\n thumb_url\n }\n gapc_brand {\n id\n name\n }\n fitted_uvdb_vehicle_definitions {\n items {\n name\n uvdb_make {\n name\n }\n uvdb_models {\n name\n }\n }\n }\n gapc_part_type {\n id\n name\n }\n interchange(limit: 500) {\n items {\n name\n barcode\n is_universal\n is_oe\n is_performance\n gtin14\n width_mm\n length_mm\n height_mm\n weight_g\n packing_unit\n quantity_per_packing_unit\n description\n mpn\n gapc_brand {\n id\n name\n }\n gapc_position {\n id\n name\n }\n gapc_images {\n large_url\n }\n gapc_attributes {\n id\n name\n value\n }\n }\n }\n }\n }\n }\n }"}';
            // echo $partnumber; die('yyyy');
            //$graphQLquery = '{"query":"{\n gapc {\n search_gapc_parts(part_number: \"'.$partnumber.'\", limit: 500, search_context: { use_fuzzy_search: false, search_interchangeable: true }) {\n items {\n id\n name\n description\n length_mm\n weight_g width_mm\n height_mm\n is_universal\n is_oe\n is_performance\n gtin14\n packing_unit\n quantity_per_packing_unit\n mpn\n products {\n items {\n store {\n name\n }\n sku\n images {\n large_url\n }\n }\n }\n part_number\n gapc_attributes {\n name\n id\n value\n }\n gapc_images {\n large_url\n thumb_url\n }\n gapc_brand {\n id\n name\n }\n gapc_position {\n id\n name\n }\n fitted_uvdb_vehicle_definitions {\n items {\n name\n uvdb_make {\n name\n }\n uvdb_models {\n name\n }\n }\n }\n gapc_part_type {\n id\n name\n }\n }\n }\n }\n }"}';
            $graphQLquery = '{"query":" {\n me {\n store_admin {\n search_products( limit: 500 part_filter: {\n part_number: \"'.$partnumber.'\", search_context: {\n search_interchangeable: true }}) {\n products {\n items {\n name\n description\n images {\n large_url\n thumb_url }\n sku\n gapc_part {\n id\n part_number\n name\n gapc_brand {\n name\n }\n }\n }\n }\n }\n }\n }\n }"}';

            $response = $client->request('post', 'https://api.partly.com/node-api/graphql', [
                'headers' => [
                    'X-Api-Key' => 'f14fa7bead031c8113ca811145db296d:2eef1dc56f3799bdcd7186ce95941af7:80ec022a1acd0251f344a3a5572ff38aa80181d5186eba4cf053df84dbcd18f0bd93110ad03474cfef38f2be388577c0ddf85f391e251363f461dc29a6bac1b87d3b5df2dc9875ae26b6a261fdd1fdd16d9a785033fc264ec8bfad7783c08fcb156f410e46d51195772bddcca8853e95b81a6936fd881c2479175898bcffe686d52e9dfbbae081442c401ba7623982faee7a13bde6993d8d44aa72e7e038571f0a3b5c785343daff6fbe',
                    'Content-Type' => 'application/json'
                ],
                'body' => $graphQLquery
            ]);

            $response->getStatusCode();
            $responseBody = $response->getBody();
            $responseContent = json_decode($responseBody->getContents());
            //echo "<pre>"; print_r($responseContent); die;
            return $responseContent->data->me->store_admin->search_products->products->items;
    }
}
