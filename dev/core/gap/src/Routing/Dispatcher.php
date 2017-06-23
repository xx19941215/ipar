<?php
namespace Gap\Routing;

class Dispatcher {
    protected $router;
    public function __construct($router) {
        $this->router = $router;
    }
    public function dispatch($request) {
        $dispatcher = new \FastRoute\Dispatcher\GroupCountBased(
            $this->router->getData()
        );

        $route = $dispatcher->dispatch(
            $request->getMethod(),
            $request->getHttpHost() . $request->getPathInfo()
        );

        switch ($route[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                throw new NotFoundHttpException();
                break;

            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedHttpException();
                break;

            case \FastRoute\Dispatcher::FOUND:
                return $this->callRoute($route, $request);
                return $route;
                break;
        }
    }
}
