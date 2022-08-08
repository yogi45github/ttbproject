<?php
/**
 *
 * Copyright © 2015 Metalcanvascommerce. All rights reserved.
 */
namespace Metalcanvas\Sechome\Controller\Customaddproduct;


use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;

class Product extends \Magento\Framework\App\Action\Action
{

	/**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;
    protected $Client;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       //Client $Client,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        //$this->Client = $Client;
        $this->_context = $context;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute() {
        die('asdas');
        $client = new Client();
        $graphQLquery = '{"query":"{\n  gapc {\n    search_gapc_parts(part_number: \"26 11 7 511 454 S1\") {\n      items {\n        id\n        part_number\n        gapc_brand {\n          id\n          name\n        }\n        interchange {\n          items {\n            part_number\n            gapc_brand {\n              id\n              name\n            }\n          }\n        }\n        fitted_uvdb_vehicle_definitions {\n          items {\n            id\n            description\n          }\n        }\n      }\n    }\n  }\n}"}';
        $graphQLquery = '{"query":"{\n uvdb {\n search_uvdb_models(uvdb_make_id: \"UMAK1372\", limit: 500) {\n items {\n id\n name\n }\n }\n }\n }"}';
        $graphQLquery = '{"query":"{\ngapc {\n search_gapc_parts(query: \"26117511454\") {\n items {\n name\n description\n fitted_uvdb_vehicle_definitions {\n items {\n name\n uvdb_make {\n name\n }\n uvdb_models {\n name\n }\n }\n }\n gapc_part_type {\n id\n name\n }\n interchange {\n items {\n name\n mpn\n gapc_brand {\n id\n name\n }\n }\n }\n }\n }\n }\n }"}';

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
        echo "<pre>";
        if(count($items) > 0){
            foreach($items as $item){
                echo "name: ".$item->name;
                echo " part type: ".$item->gapc_part_type->name;
                echo "<br>";
                $innerItems = $item->fitted_uvdb_vehicle_definitions->items;
                if(count($innerItems) > 0){
                    foreach($innerItems as $innerItem){
                        echo "name: ". $innerItem->name;
                        echo " make: ". $innerItem->uvdb_make->name;
                        echo "<br>";
                    }
                }
                echo "========<br>";

             //   die();
            }
        }
        die('asdasd');
        try {
            $isOption = null;
            $post = $this->getRequest()->getPostValue();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
            $baseUrl = $store->getBaseUrl();
            $cart = $objectManager->get('Magento\Checkout\Model\Cart');

            $productOptions = array();
            $productId =1;
            $_product = $objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
            $customOptions = $objectManager->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($_product);
            foreach ($customOptions->getData() as $key => $value) {
                $productOptions[ $value['option_id'] ] = 'Name: '.$_GET['name'].', Partnumber: '.$_GET['partnumber'];
            }
            

            $productData = [];
            $productData['qty'] = 1; 
            $productData['product'] = $productId;
            $productData['options'] = $productOptions;
            
            if ($_product) {
                $cart->addProduct($_product, $productData); 
            }
            $cart->save();
            $this->messageManager->addSuccess(__('Product added Successfully.'));
            $this->getResponse()->setRedirect( $baseUrl.'checkout/cart' );
           
            
        } catch (Exception $e) {
            $this->messageManager->addError(__('Something went wrong.'));
            $this->getResponse()->setRedirect( $this->_redirect->getRefererUrl() ); 
        }
    }
}
