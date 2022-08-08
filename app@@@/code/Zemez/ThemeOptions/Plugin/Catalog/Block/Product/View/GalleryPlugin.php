<?php

namespace Zemez\ThemeOptions\Plugin\Catalog\Block\Product\View;

use \Magento\Catalog\Block\Product\View\Gallery;
use \Zemez\ThemeOptions\Helper\Data;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;

/**
 * Config edit plugin.
 *
 * @package Zemez\ThemeOptions\Plugin\Catalog\Block\Product\View
 */
class GalleryPlugin
{
    /**
     * ThemeOptions helper.
     *
     * @var helper
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * Construct
     *
     * @param \Zemez\ThemeOptions\Helper\Data $helper
     *
     */
    public function __construct(
        Data $helper,
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder
    ) {
        $this->_helper = $helper;
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * Get product reviews summary
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function aroundGetMagnifier(
        Gallery $subject,
        callable $proceed)
    {
        $magnifierArray = $this->jsonDecoder->decode($proceed());
        $magnifierArray["width"] = $this->_helper->getProductGalleryImgWidth();
        $magnifierArray["height"] = $this->_helper->getProductGalleryImgHeight();
        $magnifierArray["enabled"] = (bool) $this->_helper->isImageZoom();

        return $this->jsonEncoder->encode($magnifierArray);
    }

}