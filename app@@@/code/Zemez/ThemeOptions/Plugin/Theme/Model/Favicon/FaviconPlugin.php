<?php

namespace Zemez\ThemeOptions\Plugin\Theme\Model\Favicon;

use \Magento\Theme\Model\Favicon\Favicon;
use \Zemez\ThemeOptions\Helper\Data;

/**
 * Config edit plugin.
 *
 * @package Zemez\ThemeOptions\Plugin\Theme\Model\Favicon
 */
class FaviconPlugin
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
     * Get favicon
     *
     * @return string
     */
    public function aroundGetFaviconFile(Favicon $subject, callable $proceed)
    {
        $favicon = $this->_helper->getFaviconUrl();
        return $favicon ? $favicon : $proceed();
    }

}