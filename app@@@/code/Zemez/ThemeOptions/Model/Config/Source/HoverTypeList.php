<?php

namespace Zemez\ThemeOptions\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Repeat
 *
 * @package Zemez\ThemeOptions\Model\Config\Source
 */
class HoverTypeList implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0,          'label' => __('Default')],
            ['value' => 'switcher', 'label' => __('Switch image')],
            ['value' => 'carousel', 'label' => __('Image carousel')]
        ];
    }
}