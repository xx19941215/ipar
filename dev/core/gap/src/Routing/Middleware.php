<?php
namespace Gap\Routing;

abstract class Middleware {
    protected $kernel;

    public function __construct($kernel) {
        $this->kernel = $kernel;
    }
    abstract function handle($route, $request);
}
