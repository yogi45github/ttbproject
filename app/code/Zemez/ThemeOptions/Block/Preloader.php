<?php

namespace Zemez\ThemeOptions\Block;

use Zemez\ThemeOptions\Helper\Data as ThemeOptionsHelper;
use Magento\Framework\View\Element\Template;
use Magento\Backend\Model\UrlInterface;

/**
 * Preloader block.
 *
 * @method string getPosition()
 *
 * @package Zemez\ThemeOptions\Block
 */
class Preloader extends Template
{
    /**
     * @var string
     */
    protected $_template = 'preloader.phtml';


    /**
     * @var ThemeOptionsHelper
     */
    protected $_helper;

    /**
     * Preloader constructor.
     *
     * @param ThemeOptionsHelper $helper
     * @param Template\Context   $context
     * @param array              $data
     */
    public function __construct(
        ThemeOptionsHelper $helper,
        Template\Context $context,
        array $data
    )
    {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }


    public function isPreloader() 
    {
        return $this->_helper->isPreloader();
    }

    public function getPreloaderType() 
    {
        return $this->_helper->getPreloaderType();
    }

    public function getPreloaderImage() 
    {
        return $this->_helper->getPreloaderImage();
    }

     /**
     * Get image url
     *
     * @return string
     */
    public function getImageUrl()
    {
        $media = $this->_urlBuilder->getBaseUrl([
            '_type' => UrlInterface::URL_TYPE_MEDIA
        ]);

        return sprintf('%s%s%s', $media, 'theme_options/', $this->getData('image'));
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        return $this->isPreloader() ? parent::_toHtml() : '';

        return parent::_toHtml();
    }

}