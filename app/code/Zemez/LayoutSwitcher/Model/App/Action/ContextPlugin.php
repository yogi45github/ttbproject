<?php

namespace Zemez\LayoutSwitcher\Model\App\Action;

use Zemez\LayoutSwitcher\Helper\Data as LayoutSwitcherHelper;

/**
 * Class ContextPlugin
 *
 * @package Zemez\LayoutSwitcher\Model\App\Action
 */
class ContextPlugin
{
    /**
     * @var LayoutSwitcherHelper
     */
    protected $_helper;

    /**
     * ContextPlugin constructor.
     *
     * @param LayoutSwitcherHelper $helper
     */
    public function __construct(LayoutSwitcherHelper $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @param $request
     *
     * @return mixed
     */
    public function aroundDispatch($subject, \Closure $proceed, $request)
    {
        $this->_helper->setHttpContext();

        return $proceed($request);
    }
}