<?php

namespace Truck\Parts\Controller\Vehicle;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Result\Page;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;

class Listaddtocart extends \Magento\Framework\App\Action\Action
{   
    protected $Client;

    public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\Controller\Result\Redirect $resultRedirectFactory
    ) {

        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        try {
            $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
            $checkoutSession  = $objectManager->get('\Magento\Catalog\Model\Session');
            $checkoutSession->setCustomProductPrice($_POST['price']);
            $checkoutSession->setCustomProductName($_POST['name']);
            $checkoutSession->setCustomProductArticalName($_POST['articalname']);
            $checkoutSession->setCustomProductBrand($_POST['brand']);
            $checkoutSession->setCustomProductPartnumber($_POST['partnumber']);


            $post = $this->getRequest()->getPostValue();
            $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
            $baseUrl = $store->getBaseUrl();
            $cart = $objectManager->get('Magento\Checkout\Model\Cart');

            $productOptions = array();
            $productId =29;
            $_product = $objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
            $customOptions = $objectManager->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($_product);
            foreach ($customOptions->getData() as $key => $value) {
                $productOptions[ $value['option_id'] ] = 'Namn: '.$post['name'].'||Artikelnummer: '.$post['articalname'].'||Fabrikat: '.$post['brand'];
            }
            

            $productData = [];
            $productData['qty'] = 1; 
            $productData['product'] = $productId;   
            $productData['options'] = $productOptions;
            
            if ($_product) {
                $cart->addProduct($_product, $productData); 
            }
            $cart->save();
            $this->messageManager->addSuccess(
                __('Produkten lades till i varukorgen')
            );
            die( true );
        } catch (Exception $e) {
            die( false );
        }

    }
}