<?php
/**
 *
 * Copyright © 2015 Metalcanvascommerce. All rights reserved.
 */
namespace Metalcanvas\Sechome\Controller\Customaddproduct;

class Addproduct extends \Magento\Framework\App\Action\Action
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
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
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
        try {

           // die('dasdasd');
            $isOption = null;
            $post = $this->getRequest()->getPostValue();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
            $baseUrl = $store->getBaseUrl();
            $cart = $objectManager->get('Magento\Checkout\Model\Cart');

            $productOptions = array();
            $productId =29;
            $_product = $objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
            $customOptions = $objectManager->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($_product);
            foreach ($customOptions->getData() as $key => $value) {
                //print_r($post);die;
                $productOptions[ $value['option_id'] ] = 'Namn: '.$post['name'].'||Artikelnummer: '.$post['articalnumber'].'||Fabrikat: '.$post['brand'];
            }
            //echo "<pre>"; print_r($productOptions);die;
            // if( isset($post['size']) ){
            //     $size = $post['size'];
            //     $optionId =  key($size);
            //     $optionValue =  $size[$optionId];
            //     $productOptions[ $optionId ] = $optionValue;
            //     $isOption = 1;
            // }

            // if( isset($post['frame']) ){
            //     $frame = $post['frame'];
            //     $optionId =  key($frame);
            //     $optionValue =  $frame[$optionId];
            //     $productOptions[ $optionId ] = $optionValue;
            //     $isOption = 1;
            // }

            // if( isset($post['finish']) ){
            //     $finish = $post['finish'];
            //     $optionId =  key($finish);
            //     $optionValue =  $finish[$optionId];
            //     $productOptions[ $optionId ] = $optionValue;
            //     $isOption = 1;
            // }

            $productData = [];
            $productData['qty'] = $post['qtyy']; 
            $productData['product'] = $productId;   
            //if($isOption){
                $productData['options'] = $productOptions;
            //} 
            
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
