<?php
namespace Gap\Validation;

class ValidationException extends \Exception {

    protected $name;

    public function setName($name)
    {
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
}
