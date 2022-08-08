<?php
/**
 * Class Image
 *
 * PHP version 7
 *
 * @category Vision
 * @package  Vision_AjaxInfiniteScroll
 * @author   Vision <magento@vision-technologies.com>
 * @license  https://www.vision-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.vision-technologies.com
 */
namespace Vision\AjaxInfiniteScroll\Model\Config\Backend;

/**
 * Class Image
 *
 * @category Vision
 * @package  Vision_AjaxInfiniteScroll
 * @author   Vision <magento@vision-technologies.com>
 * @license  https://www.vision-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.vision-technologies.com
 */
class Image extends \Magento\Config\Model\Config\Backend\Image
{
    /**
     * UPLOAD_DIR
     */
    const UPLOAD_DIR = 'vision/ajax_infinite_scroll'; // Folder save image

    /**
     * @return string
     */
    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    /**
     * @return bool
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    /**
     * @return array|string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }
}
