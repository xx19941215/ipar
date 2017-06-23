<?php
namespace Gap\Http;

abstract class Middleware {
    protected $kernel;
    public function __construct($kernel) {
        $this->kernel = $kernel;
    }

    abstract function handle($request);
}
