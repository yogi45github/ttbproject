<?php

namespace Zemez\ThemeOptions\Plugin\Theme\Block\Html;

use \Magento\Theme\Block\Html\Header;
use \Zemez\ThemeOptions\Helper\Data;

/**
 * Config edit plugin.
 *
 * @package Zemez\ThemeOptions\Plugin\Theme\Block\Html
 */
class HeaderPlugin
{
    /**
     * Config sections.
     *
     * @var helper
     */
    protected $_helper;

    /**
     * @param \Zemez\ThemeOptions\Helper\Data $helper
     *
     */
    public function __construct(
        Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * Get Welcome message
     *
     * @return string
     */
    public function aroundGetWelcome(Header $subject, callable $proceed)
    {
        $welcome = $this->_helper->getWelcome();
        return $welcome ? $welcome : $proceed();
    }

}