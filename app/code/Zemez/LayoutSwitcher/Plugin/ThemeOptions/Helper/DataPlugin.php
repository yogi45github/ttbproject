<?php

namespace Zemez\LayoutSwitcher\Plugin\ThemeOptions\Helper;

use Zemez\ThemeOptions\Helper\Data as ThemeOptionsHelper;
use Zemez\LayoutSwitcher\Helper\Data as LayoutSwitcherHelper;

/**
 * Class DataPlugin.
 *
 * @package Zemez\LayoutSwitcher\Plugin\ThemeOptions\Helper
 */
class DataPlugin
{
    /**
     * @var LayoutSwitcherHelper
     */
    protected $_helper;

    /**
     * DataPlugin constructor.
     *
     * @param LayoutSwitcherHelper $helper
     */
    public function __construct(LayoutSwitcherHelper $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Around get color scheme method plugin.
     *
     * @param ThemeOptionsHelper $subject
     * @param callable           $proceed
     *
     * @return mixed
     */
    public function aroundGetCurrentColorScheme(ThemeOptionsHelper $subject, callable $proceed)
    {
        return $this->_helper->getCurrentColorScheme() ?: $proceed();
    }
}