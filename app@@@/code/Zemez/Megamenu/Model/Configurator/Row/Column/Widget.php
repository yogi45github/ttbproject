<?php
namespace Zemez\Megamenu\Model\Configurator\Row\Column;

class Widget extends Entity
{
    public $rendererClass = 'Widget';

    public function __construct(
        array $data = []
    ) {
        parent::__construct($data);
    }

}