<?php
namespace Gap\Validation\Rules;

class Str extends AbstractRule {

    //protected $template = ':must-be-a-string';
    public function __construct() {
        $this->template = trans('must-be-a-string');
    }

    public function validate($input) {
        return is_string($input);
    }
}
