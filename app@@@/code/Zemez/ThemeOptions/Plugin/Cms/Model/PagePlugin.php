<?php

namespace Zemez\ThemeOptions\Plugin\Cms\Model;

use \Magento\Cms\Model\Page as Page;
use \Zemez\ThemeOptions\Helper\Data;

/**
 * Config edit plugin.
 *
 * @package Zemez\ThemeOptions\Plugin\Cms\Model
 */
class PagePlugin
{
    /**
     * Config sections.
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
     * Check is Homepage
     *
     * @return string
     */
    public function isHomePage($subject)
    {
        return $this->_helper->getPathHomePage() == $subject->getIdentifier();
    }

    /**
     * Get Site title for HomePage
     *
     * @return string
     */
    public function aroundGetMetaTitle(Page $subject, callable $proceed)
    {
        return $this->isHomePage($subject) ? $this->_helper->getSiteTitle() : $proceed();
    }

    /**
     * Get Keywords for HomePage
     *
     * @return string
     */
    public function aroundGetMetaKeywords(Page $subject, callable $proceed)
    {
        return $this->isHomePage($subject) ? $this->_helper->getDefaultKeywords() : $proceed();
    }

    /**
     * Get Description for HomePage
     *
     * @return string
     */
    public function aroundGetMetaDescription(Page $subject, callable $proceed)
    {
        return $this->isHomePage($subject) ? $this->_helper->getDefaultDescription() : $proceed();
    }

}