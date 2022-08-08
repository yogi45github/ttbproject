<?php

namespace Zemez\Megamenu\Model\Attribute\Frontend;

use Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend;

class Configurator extends AbstractFrontend
{
    public function getInputRendererClass()
    {
        return 'Zemez\Megamenu\Block\Data\Form\Element\Configurator';
    }
}