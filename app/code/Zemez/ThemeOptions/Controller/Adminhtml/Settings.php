<?php

namespace Zemez\ThemeOptions\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Class Settings
 *
 * @package Zemez\ThemeOptions\Controller\Adminhtml
 */
abstract class Settings extends Action
{
    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zemez_ThemeOptions::theme_options_config');
    }
}