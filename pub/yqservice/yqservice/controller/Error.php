<?php

namespace yqservice\controller;

use yqservice\controller\Controller;

/**
 * Created by YQService
 * User: YQService
 * Date: 03.04.18
 * Time: 14:43
 */
class Error extends Controller
{
    public function Display($tpl = 'error', $view = 'error')
    {
        $type = $this->input->getString('type', 'error');

        parent::Display($tpl, $type);
    }
}