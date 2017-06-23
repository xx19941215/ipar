<?php
namespace Gap\Dto;

class DtoBase
{
    public function __construct($data = null)
    {
        if ($data) {
            $this->loadData($data);
        }
    }

    protected function loadData($data)
    {
        $properties = $this->getPublicProperties();

        if (is_array($data)) {
            foreach ($properties as $prop) {
                $prop_name = $prop->name;
                $this->$prop_name = isset($data[$prop_name]) ? $data[$prop_name] : null;
            }
            return;
        }

        if (is_object($data)) {
            foreach ($properties as $prop) {
                $prop_name = $prop->name;
                $this->$prop_name = property_exists($data, $prop_name) ? $data->$prop_name : null;
            }
            return;
        }
    }

    protected function getPublicProperties()
    {
        $reflect = new \ReflectionClass($this);
        return $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
    }
}
