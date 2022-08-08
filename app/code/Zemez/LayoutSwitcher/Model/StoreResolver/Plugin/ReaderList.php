<?php

namespace Zemez\LayoutSwitcher\Model\StoreResolver\Plugin;

use Zemez\LayoutSwitcher\Helper\Data as LayoutSwitcherHelper;
use Zemez\LayoutSwitcher\Model\StoreResolver\Store as StoreResolver;
use Magento\Store\Model\StoreResolver\ReaderList as BaseReaderList;

/**
 * Class ReaderList
 *
 * @package Zemez\LayoutSwitcher\Model\StoreResolver\Plugin
 */
class ReaderList
{
    /**
     * @var LayoutSwitcherHelper
     */
    protected $_helper;

    /**
     * @var StoreResolver
     */
    protected $_storeResolver;

    /**
     * ReaderList constructor.
     *
     * @param LayoutSwitcherHelper $helper
     * @param StoreResolver        $storeResolver
     */
    public function __construct(
        LayoutSwitcherHelper $helper,
        StoreResolver $storeResolver
    )
    {
        $this->_helper = $helper;
        $this->_storeResolver = $storeResolver;
    }

    /**
     * @param BaseReaderList $subject
     * @param callable       $proceed
     * @param string         $runMode
     *
     * @return StoreResolver
     */
    public function aroundGetReader(BaseReaderList $subject, callable $proceed, $runMode)
    {
        if ($this->_helper->isEnabled()) {
            return $this->_storeResolver;
        }

        return $proceed($runMode);
    }
}