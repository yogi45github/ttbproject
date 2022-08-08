<?php

namespace Zemez\ThemeOptions\Model\Config\Source;

class TiltOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'tilt_basic', 'label' => __('Basic')],
            ['value' => 'tilt_glare',  'label' => __('Glare')],
            ['value' => 'tilt_floating',  'label' => __('Floating')],
            ['value' => 'tilt_scale',  'label' => __('Scale')],
            ['value' => 'tilt_x_axis',  'label' => __('X-Axis')],
            ['value' => 'tilt_y_axis',  'label' => __('Y-Axis')]
        ];
    }
}

