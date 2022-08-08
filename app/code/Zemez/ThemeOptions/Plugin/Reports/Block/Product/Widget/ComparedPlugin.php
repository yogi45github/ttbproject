<?php

namespace Zemez\ThemeOptions\Plugin\Reports\Block\Product\Widget;

use \Magento\Reports\Block\Product\Widget\Compared;
use \Zemez\ThemeOptions\Helper\Data;

/**
 * Config edit plugin.
 *
 * @package Zemez\ThemeOptions\Plugin\Block\Product\Compared
 */
class ComparedPlugin
{
    /**
     * ThemeOptions helper.
     *
     * @var helper
     */
    protected $_helper;

    /**
     * Construct
     *
     * @param \Zemez\ThemeOptions\Helper\Data $helper
     *
     */
    public function __construct(
        Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * Get Description for HomePage
     *
     * @return string
     */
    public function aroundToHtml(Compared $subject, callable $proceed)
    {
        return $this->_helper->isRecentlyCompared() ? $proceed() : false;
    }

}