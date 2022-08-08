<?php

namespace Zemez\LayoutSwitcher\Model\Config\Source;

use Magento\Store\Model\ResourceModel\Store\Collection;

/**
 * Store source model.
 *
 * @package Zemez\LayoutSwitcher\Model\Config\Source
 */
class Store extends Collection
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('code', 'name', ['website_id' => 'website_id']);
    }
}