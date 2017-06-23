<?php
namespace Ipar\Search\Dto;

class IparSearchDtoBase extends \Gap\Dto\DtoBase
{
    protected $highlights = [];
    protected $xs;

    public function __construct($data, $xs)
    {
        $this->xs = $xs;
        $this->loadData($data);
    }

    protected function loadData($data)
    {
        if ($data instanceof \XSDocument) {
            $properties = $this->getPublicProperties();
            foreach ($properties as $prop) {
                $prop_name = $prop->name;
                $this->$prop_name = isset($this->highlights[$prop_name]) ? $this->xs->highlight($data->$prop_name) : $data->$prop_name;
            }
            return;
        }

        parent::loadData($data);
    }
}
