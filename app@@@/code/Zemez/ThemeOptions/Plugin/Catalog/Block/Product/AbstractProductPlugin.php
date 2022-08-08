<?php

namespace Zemez\ThemeOptions\Plugin\Catalog\Block\Product;

use \Magento\Catalog\Block\Product\AbstractProduct;
use \Zemez\ThemeOptions\Helper\Data;
use \Magento\Catalog\Helper\Image as ImageHelper;
use \Magento\Framework\View\LayoutInterface;
use \Magento\Catalog\Block\Product\View\Gallery;
use \Magento\Framework\Json\EncoderInterface;

/**
 * Config edit plugin.
 *
 * @package Zemez\ThemeOptions\Plugin\Catalog\Block\Product
 */
class AbstractProductPlugin
{
    /**
     * ThemeOptions helper.
     *
     * @var helper
     */
    protected $_helper;

    protected $_imageHelper;
    public $_layout;
    public $_gallery;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\ProductVideo\Helper\Media
     */
    protected $mediaHelper;

    /**
     * Construct
     *
     * @param \Zemez\ThemeOptions\Helper\Data $helper
     *
     */
    public function __construct(
        Data $helper,
        ImageHelper $imageHelper,
        LayoutInterface $layout,
        Gallery $gallery,
        EncoderInterface $jsonEncoder,
        \Magento\ProductVideo\Helper\Media $mediaHelper
    ) {
        $this->_helper = $helper;
        $this->_imageHelper = $imageHelper;
        $this->_layout = $layout;
        $this->_gallery = $gallery;
        $this->jsonEncoder = $jsonEncoder;
        $this->mediaHelper = $mediaHelper;
    }

    /**
     * Get if it is necessary to show product stock status
     *
     * @return bool
     */
    public function aroundDisplayProductStockStatus(AbstractProduct $subject, callable $proceed)
    {
        return $this->_helper->isProductShowStock() ? $proceed() : '';
    }

    public function aroundGetImage(AbstractProduct $subject, callable $proceed, $product, $imageId, $attributes)
    {
        $image = $proceed($product, $imageId, $attributes);
        if($imageId != 'category_page_grid' && $imageId != 'category_page_list') return $image;
        if ($imageId == 'category_page_grid') {
            $hoverType = $this->_helper->getHoverType();
        } elseif($imageId == 'category_page_list') {
            $hoverType = $this->_helper->getListHoverType();
        };


        switch ($hoverType) {
            case ('switcher'):
                /**
                 * @var $productImg \Magento\Catalog\Block\Product\Image
                 */
                $image->setTemplate('Zemez_ThemeOptions::product/switcher.phtml');
                $product->load($product->getId());
                $onHover = $product->getCustomAttribute('on_hover');
                $video = $this->_getVideoUrl($product);
                $attributeImageUrl = false;
                if($onHover && $onHover->getValue() != 'no_selection') {
                    $attributeImageUrl = $video
                        ? false
                        : $this->_imageHelper->init($product, $imageId)->setImageFile($onHover->getValue())->getUrl();
                }
                $image->setVideoUrl($video);
                $image->setReplaceImageUrl($attributeImageUrl);
                break;
            case ('carousel'):
                /** @var $productImg \Magento\Catalog\Block\Product\View\Gallery */
                $image = $this->_layout->createBlock('Zemez\ThemeOptions\Block\Product\View\Gallery')
                                   ->setTemplate('Zemez_ThemeOptions::product/carousel.phtml');
                $this->_setupGallery($product, $image);
                break;
            case ('gallery'):
                /** @var $productImg \Magento\Catalog\Block\Product\View\Gallery */
                $image = $this->_layout->createBlock('Zemez\ThemeOptions\Block\Product\View\Gallery')
                                   ->setTemplate('Zemez_ThemeOptions::product/gallery.phtml');
                $this->_setupGallery($product, $image);
                break;
            case (0):
                $this->_imageHelper->init($product, $imageId)->getUrl();
                break;
            default: break;
        }
        return $image;
    }

    protected function _setupGallery($product, $image)
    {
        $product->load($product->getId());
        $image->setProduct($product);
        $image->setVideoData($this->getGalleryDataJson($product));
        $image->setVideoSettings($this->getVideoSettings());
    }


    /**
     * Retrieve media gallery data in JSON format
     *
     * @return string
     */
    public function getGalleryDataJson($product)
    {
        $mediaGalleryData = [];
        foreach ($product->getMediaGalleryImages() as $mediaGalleryImage) {
            $mediaGalleryData[] = [
                'mediaType' => $mediaGalleryImage->getMediaType(),
                'videoUrl' => $mediaGalleryImage->getVideoUrl(),
                'isBase' => $this->isMainImage($product, $mediaGalleryImage),
            ];
        }
        return $this->jsonEncoder->encode($mediaGalleryData);
    }

    /**
     * Retrieve video settings data in JSON format
     *
     * @return string
     */
    public function getVideoSettings()
    {
        $videoSettingData[] = [
            'playIfBase' => $this->mediaHelper->getPlayIfBaseAttribute(),
            'showRelated' => $this->mediaHelper->getShowRelatedAttribute(),
            'videoAutoRestart' => $this->mediaHelper->getVideoAutoRestartAttribute(),
        ];
        return $this->jsonEncoder->encode($videoSettingData);
    }

    /**
     * Is product main image
     *
     * @param \Magento\Framework\DataObject $image
     * @return bool
     */
    public function isMainImage($product, $image)
    {
        return $product->getImage() == $image->getFile();
    }

    protected function _getVideoUrl($product)
    {
        $video = false;
        foreach ($product->getMediaGalleryImages()->getItems() as $image) {
            if($image->getMediaType() == 'external-video') {
                $videoUrl = $image->getVideoUrl();
                $search     = '/youtube\.com\/watch\?v=([a-zA-Z0-9]+)/smi';
                $replace    = "youtube.com/embed/$1";

                $onHoverValue = $product->getCustomAttribute('on_hover');
                $video = ($onHoverValue && $image->getFile() == $onHoverValue->getValue())
                    ? preg_replace($search,$replace,$videoUrl)
                    : false;
            }


        }
        return $video;
    }

}