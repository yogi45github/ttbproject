<?php
/**
 * Class AjaxInfiniteScroll
 *
 * PHP version 7
 *
 * @category Vision
 * @package  Vision_AjaxInfiniteScroll
 * @author   Vision <magento@visionvivante.com/.com>
 * @license  https://visionvivante.com/  Open Software License (OSL 3.0)
 * @link     https://visionvivante.com/
 */
namespace Vision\AjaxInfiniteScroll\Block;

/**
 * Class AjaxInfiniteScroll
 *
 * @category Vision
 * @package  Vision_AjaxInfiniteScroll
 * @author   Vision <magento@visionvivante.com/>
 * @license  https://visionvivante.com/  Open Software License (OSL 3.0)
 * @link     https://visionvivante.com/
 */
class AjaxInfiniteScroll extends \Magento\Framework\View\Element\Template
{
    /**
     * StoreManagerInterface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * ScopeConfigInterface
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * XML_PATH_ENABLED
     */
    const XML_PATH_ENABLED = 'vision_ajax_infinite_scroll/general/enabled';

    /**
     * XML_PATH_DELAY
     */
    const XML_PATH_DELAY = 'vision_ajax_infinite_scroll/selectors/delay';

    /**
     * XML_PATH_CONTENT
     */
    const XML_PATH_CONTENT = 'vision_ajax_infinite_scroll/selectors/content';

    /**
     * XML_PATH_PAGINATION
     */
    const XML_PATH_PAGINATION = 'vision_ajax_infinite_scroll/selectors/pagination';

    /**
     * XML_PATH_NEXT
     */
    const XML_PATH_NEXT = 'vision_ajax_infinite_scroll/selectors/next';

    /**
     * XML_PATH_ITEM
     */
    const XML_PATH_ITEM = 'vision_ajax_infinite_scroll/selectors/item';

    /**
     * XML_PATH_LOADING_IMAGE
     */
    const XML_PATH_LOADING_IMAGE = 'vision_ajax_infinite_scroll/design/loading_image';

    /**
     * XML_PATH_LOADING_TEXT
     */
    const XML_PATH_LOADING_TEXT = 'vision_ajax_infinite_scroll/design/loading_text';

    /**
     * XML_PATH_DONE_TEXT
     */
    const XML_PATH_DONE_TEXT = 'vision_ajax_infinite_scroll/design/done_text';

    /**
     * AjaxInfiniteScroll constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * IsEnabled
     *
     * @return mixed
     */
    public function isEnabled()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLED, $storeScope);
    }

    /**
     * Dely
     *
     * @return mixed
     */
    public function dely()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_DELAY, $storeScope);
    }

    /**
     * Content
     *
     * @return mixed
     */
    public function content()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_CONTENT, $storeScope);
    }

    /**
     * Pagination
     *
     * @return mixed
     */
    public function pagination()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_PAGINATION, $storeScope);
    }

    /**
     * Next
     *
     * @return mixed
     */
    public function next()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_NEXT, $storeScope);
    }

    /**
     * Item
     *
     * @return mixed
     */
    public function item()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_ITEM, $storeScope);
    }

    /**
     * LoadingImage
     *
     * @return mixed
     */
    public function loadingImage()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_LOADING_IMAGE, $storeScope);
    }

    /**
     * LoadingText
     *
     * @return mixed
     */
    public function loadingText()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_LOADING_TEXT, $storeScope);
    }

    /**
     * GetdoneText
     *
     * @return mixed
     */
    public function doneText()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_DONE_TEXT, $storeScope);
    }

    /**
     * GetMediaPath
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaPath()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
