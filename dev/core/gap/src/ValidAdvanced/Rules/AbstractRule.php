<?php
namespace Gap\Validation\Rules;

use Gap\Validation\Validatable;

abstract class AbstractRule implements Validatable {
    protected $name;
    protected $template;

    public function __construct() {
        //required by REflectionClass::newInstance()
    }
    public function assert($input) {
        if ($this->validate($input)) {
            return true;
        }
        throw $this->reportError($input);
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function getName() {
        return $this->name;
    }
    public function setTemplate($template) {
        $this->template = $template;
    }
    public function reportError($input, array $extraParams = []) {
        $exception = new \Gap\Validation\ValidationException($this->template);
        $exception->setName($this->name);
        return $exception;
    }
}
