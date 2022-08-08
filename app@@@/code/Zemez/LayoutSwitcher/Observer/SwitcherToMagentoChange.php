<?php

namespace Zemez\LayoutSwitcher\Observer;

use Zemez\LayoutSwitcher\Helper\Data as LayoutSwitcherHelper;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SwitcherToMagentoChange
 *
 * @package Zemez\LayoutSwitcher\Observer
 */
class SwitcherToMagentoChange implements ObserverInterface
{
    /**
     * @var LayoutSwitcherHelper
     */
    protected $_layoutSwitcherHelper;

    /**
     * @var WebsiteRepositoryInterface
     */
    protected $_websiteRepository;

    /**
     * @var StoreRepositoryInterface
     */
    protected $_storeRepository;
    protected $_storeManager;

    /**
     * SwitcherToMagentoChange constructor.
     *
     * @param LayoutSwitcherHelper       $layoutSwitcherHelper
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param StoreRepositoryInterface   $storeRepository
     */
    public function __construct(
        LayoutSwitcherHelper $layoutSwitcherHelper,
        WebsiteRepositoryInterface $websiteRepository,
        StoreRepositoryInterface $storeRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_layoutSwitcherHelper = $layoutSwitcherHelper;
        $this->_websiteRepository = $websiteRepository;
        $this->_storeRepository = $storeRepository;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param Observer $observer
     *
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $websiteCode = $this->_layoutSwitcherHelper->getDefaultTheme();
        $storeCode = $this->_layoutSwitcherHelper->getDefaultHomepage();
        if (!$storeCode) {
            $storeCode = $this->_storeManager->getStore()->getCode();
        }

        /** @var \Magento\Store\Model\Website $website */
        $website = $this->_websiteRepository->get($websiteCode);
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->_storeRepository->get($storeCode);
        /** @var \Magento\Store\Model\Group $group */
        $group = $store->getGroup();

        if ($group->getDefaultStoreId() !== $store->getId()) {
            $group->setDefaultStoreId($store->getId());
            $group->save();
        }

        $websiteChanged = false;
        if (!$website->getIsDefault()) {
            $website->setIsDefault(true);
            $websiteChanged = true;
        }
        if ($website->getDefaultGroupId() !== $group->getId()) {
            $website->setDefaultGroupId($group->getId());
            $websiteChanged = true;
        }
        if ($websiteChanged) {
            $website->save();
        }
    }
}