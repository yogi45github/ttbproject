<?php

/**
 *
 * Copyright © 2019 Zemez. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Zemez\AjaxCatalog\Plugin\CatalogSearch\Advanced;

use Zemez\AjaxCatalog\Helper\Catalog\View\ContentAjaxResponse;

class Result
{

    /**
     * @var ContentAjaxResponse
     */
    protected $_helper;

    public function __construct(ContentAjaxResponse $helper)
    {
        $this->_helper = $helper;
    }

    public function aroundExecute($subject, \Closure $proceed)
    {
        $request = $subject->getRequest();
        if ($request->isXmlHttpRequest()) {

            return $this->_helper->getAjaxSearchResult($subject, $proceed);
        } else {
            $returnValue = $proceed();

            return $returnValue;
        }
    }

}
