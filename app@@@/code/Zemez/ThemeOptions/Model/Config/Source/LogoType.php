<?php

namespace Zemez\ThemeOptions\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Repeat
 *
 * @package Zemez\ThemeOptions\Model\Config\Source
 */
class LogoType implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'image', 'label' => __('Image')],
            ['value' => 'text',  'label' => __('Text')]
        ];
    }
}