<?php

namespace Zemez\ThemeOptions\Model\Config\Source;

use Zemez\ThemeOptions\Helper\ColorScheme as ColorSchemeHelper;
use Magento\Framework\Option\ArrayInterface;

class ColorScheme implements ArrayInterface
{
    protected $_colorSchemeHelper;

    public function __construct(ColorSchemeHelper $colorSchemeHelper)
    {
        $this->_colorSchemeHelper = $colorSchemeHelper;
    }

    public function toOptionArray()
    {
        $options = [];
        foreach ($this->_colorSchemeHelper->getColorSchemes() as $website => $scheme) {
//            $options[$website]['disable'] = [
//                'label' => 'Disable',
//                'value' => 0
//            ];
            foreach ($scheme as $id => $data) {
                $options[$website][$id] = [
                    'label' => $data['label'],
                    'value' => $id
                ];
            }
        }

        return $options;
    }
}