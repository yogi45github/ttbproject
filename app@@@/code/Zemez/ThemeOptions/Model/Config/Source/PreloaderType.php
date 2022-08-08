<?php

namespace Zemez\ThemeOptions\Model\Config\Source;

class PreloaderType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'css_preloader', 'label' => __('CSS3 preloader')],
            ['value' => 'image_preloader',  'label' => __('Image preloader')]
        ];
    }
}

