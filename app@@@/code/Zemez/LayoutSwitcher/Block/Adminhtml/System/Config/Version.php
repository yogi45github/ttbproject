<?php

namespace Zemez\LayoutSwitcher\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Module\FullModuleList;

/**
 * Version frontend model
 *
 * @package Zemez\LayoutSwitcher\Block\Adminhtml\System\Config
 */
class Version extends Field
{
    /**
     * @var FullModuleList
     */
    protected $fullModuleList;

    /**
     * @con MODULE_NAME_VERSION
     */
    const MODULE_NAME_VERSION = 'Zemez_LayoutSwitcher';

    /**
     * Version constructor.
     *
     * @param FullModuleList $fullModuleList
     * @param Context        $context
     * @param array          $data
     */
    public function __construct(FullModuleList $fullModuleList, Context $context, array $data = [])
    {
        $this->fullModuleList = $fullModuleList;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setValue(
            $this->getModuleVersion()
        );

        return parent::_getElementHtml($element);
    }

    /**
     * Get current module version
     *
     * @return string
     */
    protected function getModuleVersion()
    {
        $module = $this->fullModuleList->getOne(self::MODULE_NAME_VERSION);

        if (null === $module) {
            return 'n/a';
        }

        return $module['setup_version'];
    }
}