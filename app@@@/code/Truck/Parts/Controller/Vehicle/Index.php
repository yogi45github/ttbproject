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

class Index extends \Magento\Framework\App\Action\Action
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        if($customerSession->isLoggedIn()){
            $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $block = $page->getLayout()->getBlock('parts_vehicle_index');
            $block->setData('items', 'h');
            return $page;
        }
        else{
            $resultRedirect = $this->resultRedirectFactory->create();
            $redirectLink = $storeManager->getStore()->getBaseUrl()."customer/account/login"; 
            $resultRedirect->setUrl($redirectLink);
            return $resultRedirect;
        }
    }
}