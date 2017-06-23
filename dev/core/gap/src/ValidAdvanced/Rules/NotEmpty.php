<?php
namespace Gap\Validation\Rules;

class NotEmpty extends AbstractRule {
    public function __construct() {
        $this->template = trans('must-not-be-empty');
    }

    public function validate($input) {
        if (is_string($input)) {
            $input = trim($input);
        }
        return !empty($input);
    }
}
