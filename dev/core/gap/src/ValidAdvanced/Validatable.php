<?php
namespace Gap\Validation;

interface Validatable {
    public function assert($input);
    public function validate($input);
    public function getName();
    public function setName($name);
    public function setTemplate($template);
    public function reportError($input, array $relatedExceptions = []);
}
