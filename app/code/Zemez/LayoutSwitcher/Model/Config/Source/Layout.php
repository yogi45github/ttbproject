<?php

namespace Zemez\LayoutSwitcher\Model\Config\Source;

use Zemez\LayoutSwitcher\Helper\Data as LayoutSwitcherHelper;
use Magento\Framework\Option\ArrayInterface;

/**
 * Layout source model.
 *
 * @package Zemez\LayoutSwitcher\Model\Config\Source
 */
class Layout implements ArrayInterface
{
    /**
     * @var LayoutSwitcherHelper
     */
    protected $_helper;

    /**
     * @var null|string
     */
    protected $_type;

    /**
     * Layout constructor.
     *
     * @param LayoutSwitcherHelper $helper
     * @param string|null          $type
     */
    public function __construct(LayoutSwitcherHelper $helper, $type = null)
    {
        $this->_helper = $helper;
        $this->_type = $type;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->_helper->getLayouts($this->_type) as $id => $data) {
            $options[] = [
                'label' => $data['label'],
                'value' => $id
            ];
        }

        return $options;
    }
}