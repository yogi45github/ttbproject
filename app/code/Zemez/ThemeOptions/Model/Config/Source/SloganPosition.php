<?php

namespace Zemez\ThemeOptions\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Repeat
 *
 * @package Zemez\ThemeOptions\Model\Config\Source
 */
class SloganPosition implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'top',    'label' => __('Top')],
            ['value' => 'bottom', 'label' => __('Bottom')],
            ['value' => 'left',   'label' => __('Left')],
            ['value' => 'right',  'label' => __('Right')]
        ];
    }
}