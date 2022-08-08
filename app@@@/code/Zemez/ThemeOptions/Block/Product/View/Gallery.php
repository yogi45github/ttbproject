<?php
/**
 * Created by PhpStorm.
 * User: impro
 * Date: 28/02/17
 * Time: 14:53
 * Simple product data view
 *
 */
namespace Zemez\ThemeOptions\Block\Product\View;

use \Zemez\ThemeOptions\Helper\Data;

class Gallery extends \Magento\Catalog\Block\Product\View\Gallery
{

    /**
     * ThemeOptions helper.
     *
     * @var helper
     */
    protected $_helper;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $arrayUtils, $jsonEncoder, $data);
    }


    public function getCategoryPageImageMode()
    {
        $categoryPageImageMode = 'category_page_grid';
        if ($this->_helper->getMode() == 'list') {
            $categoryPageImageMode = 'category_page_list';
        }
        return $categoryPageImageMode;
    }
    public function getGalleryImages()
    {
        $product = $this->getProduct();
        $images = $product->getMediaGalleryImages();
        if ($images instanceof \Magento\Framework\Data\Collection) {
            foreach ($images as $image) {
                /* @var \Magento\Framework\DataObject $image */
                $image->setData(
                    'small_image_url',
                    $this->_imageHelper->init($product, 'product_page_image_small')
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
                $image->setData(
                    'medium_image_url',
                    $this->_imageHelper->init($product, $this->getCategoryPageImageMode())
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
                $image->setData(
                    'large_image_url',
                    $this->_imageHelper->init($product, 'product_page_image_large_no_frame')
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
            }
        }

        return $images;
    }

    /**
     * Retrieve product images in JSON format
     *
     * @return string
     */
    public function getGalleryImagesJson()
    {
        $imagesItems = [];
        $imagesItemsCount = $this->_helper->getSlidesCount();
        $counter = 1;
        foreach ($this->getGalleryImages() as $image) {
            if(!empty($imagesItemsCount) && $counter > $imagesItemsCount) break;
            $counter++;
            $imagesItems[] = [
                'thumb' => $image->getData('small_image_url'),
                'img' => $image->getData('medium_image_url'),
                'full' => $image->getData('large_image_url'),
                'caption' => $image->getLabel(),
                'position' => $image->getPosition(),
                'isMain' => $this->isMainImage($image),
            ];
        }
        if (empty($imagesItems)) {
            $imagesItems[] = [
                'thumb' => $this->_imageHelper->getDefaultPlaceholderUrl('thumbnail'),
                'img' => $this->_imageHelper->getDefaultPlaceholderUrl('image'),
                'full' => $this->_imageHelper->getDefaultPlaceholderUrl('image'),
                'caption' => '',
                'position' => '0',
                'isMain' => true,
            ];
        }
        return json_encode($imagesItems);
    }

}
