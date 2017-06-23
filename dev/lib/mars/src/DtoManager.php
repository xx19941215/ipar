<?php
namespace Mars;

class DtoManager {
    protected $dto_config;

    public function __construct($dto_config)
    {
        $this->dto_config = $dto_config;
    }
    public function get($name)
    {
        if ($class = $this->dto_config->get($name)) {
            return "\\" . $class;
            //return $class[0] === "\\" ? $class : "\\Tos\\Dto\\" . $class;
        } else {
            return null;
        }
    }
}
