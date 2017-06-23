<?php
namespace Gap\Validation;

class Validator {
    protected $name;
    protected $rules;

    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    public function __call($method, array $args = []) {
        $rule_class_name = "\\Gap\\Validation\\Rules\\" . ucfirst($method);
        if (! class_exists($rule_class_name)) {
            throw new ValidationException(trans('"%s"-is-not-a-valid-rule-name', $ruleName));
        }
        $ref = new \ReflectionClass($rule_class_name);
        if (! $ref->isSubClassOf('Gap\\Validation\\Validatable')) {
            throw new ValidationException(trans('"%s"-is-not-a-respect-rule', $rule_class_name));
        }
        $rule = $ref->newInstanceArgs($args);

        $this->addRule($rule);

        return $this;
    }
    public function addRule($rule) {
        $this->rules[] = $rule;
    }
    public function assert($input) {
        foreach ($this->rules as $rule) {
            $rule->setName($this->name);
            $rule->assert($input);
        }
    }
    public function validate($input) {
        foreach ($this->rules as $rule) {
            if (!$rule->validate($input)) {
                $rule->setName($this->name);
                return false;
            }
        }
        return true;
    }
}
