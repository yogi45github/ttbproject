<?php
/**
 * Created by YQService
 * User: YQService
 * Date: 27.03.18
 * Time: 9:52
 */

namespace yqservice\controller;

use yqservice\controller\Controller;


/**
 * @property int total
 */
class Listoemparts extends Controller
{
    public function Display($tpl = 'listoemparts', $view = 'view')
    {
        $catalog = $this->input->getString('c', '');
        $ssd     = $this->input->getString('ssd', '');
        $vid     = $this->input->getString('vid', '');

        $params = ['c' => $catalog, 'ssd' => $ssd, ''];

        $requests = [
            'appendListOemParts' => [
                'vid' => $vid,
            ]
        ];

        $data = $this->getData($requests, $params);

        if ($data) {
            $this->data  = $data[0]->oemParts;
            $this->total = count($data[0]->oemParts);
        }

        parent::Display($tpl, $view);
    }
}