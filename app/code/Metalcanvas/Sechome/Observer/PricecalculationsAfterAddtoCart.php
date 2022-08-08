<?php
namespace Metalcanvas\Sechome\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
 
class PricecalculationsAfterAddtoCart implements ObserverInterface
{
     
    public function execute(\Magento\Framework\Event\Observer $observer) 
    {
        $item = $observer->getEvent()->getData('quote_item');         
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );

        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutSession  = $objectManager->get('\Magento\Catalog\Model\Session');
        $price = (float)$checkoutSession->getCustomProductPrice();
        $name = $checkoutSession->getCustomProductName();
        $brand = $checkoutSession->getCustomProductBrand();
        $artical = $checkoutSession->getCustomProductArticalName();
        $partNumber = $checkoutSession->getCustomProductPartnumber();
        if($item->getProduct()->getId() == 29){
            $item->setName($name);
            $item->setDescription(json_encode(['name' => $name, 'brand' => $brand, 'partNumber' => $partNumber, 'artical' => $artical]));
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
        }

        $checkoutSession->unsCustomProductPrice();
        $checkoutSession->unsCustomProductName();
        $checkoutSession->unsCustomProductBrand();
        $checkoutSession->unsCustomProductPartnumber();

    }
}