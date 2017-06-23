<?php
namespace Gap\Validation\Rules;

class Email extends AbstractRule {

    public function __construct() {
        $this->template = trans('must-be-valid-email');
    }

    public function validate($input) {
        return is_string($input) && filter_var($input, FILTER_VALIDATE_EMAIL);
    }
}
