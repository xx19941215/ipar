<?php
namespace Gap\Validation\Rules;

class Length extends AbstractRule {
    protected $template = ':too-short';

    protected $min;
    protected $max;

    public function __construct($min = null, $max = null) {
        $min = (int) $min;
        $max = (int) $max;

        $this->min = $min;
        $this->max = $max;

        if ($max && $max < $min) {
            throw new \Gap\Validation\ValidationException(
                trans('%d-cannot-be-less-than-%d', $min, $max)
            );
        }

    }
    public function validate($input) {
        if (is_string($input)) {
            $len = mb_strlen($input);
        } else if (is_array($input) || $input instanceof \Countable) {
            $len = count($input);
        } else if (is_object($input)) {
            $len = count(get_object_vars($input));
        } else {
            $this->template = trans('not-countable');
            return false;
        }
        if ($len < $this->min || $len > $this->max) {
            $this->template = trans('must-have-length-between-%d-and-%d', $this->min, $this->max);
            return false;
        }
        return true;
    }
}
