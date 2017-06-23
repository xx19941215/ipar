<?php
namespace Gap\Dto;

class DtoManager
{
    protected $dto_config;

    public function __construct($dto_config)
    {
        $this->dto_config = $dto_config;
    }
    public function get($name)
    {
        if ($class = $this->dto_config->get($name)) {
            return $class;
        }

        return null;
    }

    public function make($name, $data = null)
    {
        if (!$class = $this->get($name)) {
            return null;
        }

        if (!$data) {
            return new $class();
        }

        return new $class($data);
    }
}
