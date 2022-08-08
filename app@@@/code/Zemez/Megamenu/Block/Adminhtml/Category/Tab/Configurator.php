<?php
namespace Zemez\Megamenu\Block\Adminhtml\Category\Tab;

use \Magento\Backend\Helper\Data as BackandHelper;

class Configurator extends \Magento\Backend\Block\Template
{
    const CONFIGURATOR_TEMPLATE = 'Zemez_Megamenu::category/tab/configurator.phtml';

    const FILED_NAME = 'mm_configurator';

    protected $_staticBlocksSource;

    protected $registry;

    protected $_request;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $helperBackend;

    public function __construct(
        BackandHelper $helperBackend,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\Category\Attribute\Source\Page $staticBlocksSource,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperBackend = $helperBackend;
        $this->registry = $registry;
        $this->_request = $request;
        $this->_staticBlocksSource = $staticBlocksSource;
    }

    public function getTemplate(){
        return self::CONFIGURATOR_TEMPLATE;
    }

    public function toHtml()
    {
        return $this->_toHtml();
    }

    public function getStaticBlocksJson()
    {
        $blocks = $this->_staticBlocksSource->getAllOptions();
        array_shift($blocks);
        return json_encode(\Zend_Json::encode($blocks));
    }

    /**
     * name admin
     * @return null|string
     */
    public function getFrontName()
    {
        return $this->_request->getFrontName();
    }

    public function getFieldName()
    {
        return self::FILED_NAME;
    }

    public function getCategory()
    {
        return $this->registry->registry('category');
    }

    public function getConfiguredValue()
    {
        return $this->getCategory()->getMmConfigurator();
    }

    public function getCategoryLevel()
    {
        return $this->getCategory()->getLevel();
    }

    public function getAdminUrl()
    {
        echo $this->helperBackend->getHomePageUrl();
    }
}