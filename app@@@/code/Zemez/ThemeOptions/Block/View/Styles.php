<?php

namespace Zemez\ThemeOptions\Block\View;

use Magento\Framework\View\Element\Template;
use Zemez\ThemeOptions\Helper\Data as OptionsHelper;

/**
 * Theme options styles block.
 *
 * @package Zemez\ThemeOptions\Block\View
 */
class Styles extends Template
{
    /**
     * @var OptionsHelper
     */
    protected $_optionsHelper;

    /**
     * @var string
     */
    protected $_template = 'dynamic-styles.phtml';

    /**
     * Styles constructor.
     *
     * @param OptionsHelper    $helper
     * @param Template\Context $context
     * @param array            $data
     */
    public function __construct(
        OptionsHelper $helper,
        Template\Context $context,
        array $data = []
    )
    {
        $this->_optionsHelper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get product item width.
     *
     * @param int $itemsCount
     * @return string
     */
    public function getProductItemWidth($itemsCount)
    {
        return 100 / $itemsCount . '%';
    }

    /**
     * Get website Code.
     *
     * @return string
     */
    public function getWebsiteCode()
    {
        return $this->_storeManager->getWebsite()->getCode();
    }

}