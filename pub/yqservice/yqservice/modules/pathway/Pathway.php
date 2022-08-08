<?php
/**
 * Created by YQService.
 * User: YQService
 * Date: 17.08.17
 * Time: 16:03
 */

namespace yqservice\modules\pathway;


class Pathway
{
    protected static $instances = [];
    protected $_pathway = [];
    protected $_count = 0;

    public function __construct($options = [])
    {
        return $this->_pathway;
    }

    public function getPathway()
    {
        $pw = $this->_pathway;

        return array_values($pw);
    }

    public function setPathway($pathway)
    {
        $oldPathway = $this->_pathway;

        $this->_pathway = array_values((array)$pathway);

        return array_values($oldPathway);
    }

    public function getPathwayNames()
    {
        $names = [];

        foreach ($this->_pathway as $item) {
            $names[] = $item->name;
        }

        return array_values($names);
    }

    public function addItem($name, $link = '')
    {
        $ret = false;

        if ($this->_pathway[] = $this->makeItem($name, $link)) {
            $ret = true;
            $this->_count++;
        }

        return $ret;
    }

    protected function makeItem($name, $link)
    {
        $item       = new \stdClass;
        $item->name = html_entity_decode($name, ENT_COMPAT, 'UTF-8');
        $item->link = $link;

        return $item;
    }

    public function setItemName($id, $name)
    {
        $ret = false;

        if (isset($this->_pathway[$id])) {
            $this->_pathway[$id]->name = $name;
            $ret                       = true;
        }

        return $ret;
    }

    protected function _makeItem($name, $link)
    {
        return $this->makeItem($name, $link);
    }
}