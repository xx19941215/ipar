<?php
namespace Gap\Http;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class Kernel implements HttpKernelInterface {

    protected $config;
    protected $router;
    protected $service_manager;

    public function __construct($config)
    {
        $this->config = $config;
    }
    public function setRouter($router)
    {
        $this->router = $router;
        return $this;
    }
    public function getRouter()
    {
        return $this->router;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $request->setSessionConfig($this->config->get('session'));

        try {
            $this->handleHttpMiddlewares($request);

            $route = $this->router->dispatch($request);

            $this->handleRouteMiddlewares($route, $request);

            return $this->dispatch($route, $request);

        } catch (\Gap\Routing\Exception\NotLoginException $e) {
            $request->getSession()->set('url_target', $request->getUri());
            $url = $this->router->getUrlByRouteName('login');
            //$url = $this->router->getUrlByRouteName('login') . '?' . http_build_query(['target' => $request->getUri()]);
            return new \Symfony\Component\HttpFoundation\RedirectResponse($url);
        } catch (\Exception $e) {
            echo $e->getMessage();
            print_r($e);
            //_debug('todo');
        }
        return;
    }
    public function dispatch($route, $request)
    {
        $handle = $route[1];
        $params = $route[2];

        $action = prop($handle, 'action');
        $as = prop($handle, 'as');
        $mode = prop($handle, 'mode', 'ui');
        $access = prop($handle, 'access');
        $roles = prop($handle, 'roles');

        list($class, $fun) = explode('@', $action);
        if ($mode === 'rest') {
            $class = "\\Tos\\Rest\\" . $class;
        } else {
            $class = "\\Tos\\Ui\\" . $class;
        }
        $controller = new $class($request);
        $controller->setParams($params);

        $view_engine = $this->getViewEngine();
        $view_engine->useData([
            'config' => $this->config,
            'request' => $request
        ]);
        $controller->setViewEngine($view_engine);
        $controller->setServiceManager($this->getServiceManager());
        $controller->setKernel($this);
        $controller->setConfig($this->config);

        $rs = $controller->bootstrap();
        if (!$rs) {
            $rs = $controller->$fun();
        }
        return $this->getResponse($rs);
    }

    public function handleHttpMiddlewares($request)
    {
        $http_middlewares = $this->config->get('middleware.http')->all();
        foreach ($http_middlewares as $middleware) {
            $class = "\\Tos\\Middleware\\Http\\" . $middleware;
            $instance = new $class($this);
            $rs = $instance->handle($request);
        }
    }
    public function handleRouteMiddlewares($route, $request)
    {
        $route_middlewares = $this->config->get('middleware.route')->all();
        foreach ($route_middlewares as $middleware) {
            $class = "\\Tos\\Middleware\\Router\\" . $middleware;
            $instance = new $class($this);
            $rs = $instance->handle($route, $request);
        }
    }



    public function terminate(Request $request, Response $response)
    {
        //echo 'http kernel terminate';
    }


    public function getViewEngine()
    {
        $foil = \Foil\Foil::boot(
            [
                'folders' => $this->config->get('view.folders')->all(),
                'autoescape' => false,
                'ext' => 'phtml',
            ]
        );
        $view_engine = $foil->engine();
        return $view_engine;
    }
    public function getServiceManager()
    {
        if (!$this->service_manager) {
            $service_manager = new \Gap\Service\ServiceManager($this->config->get('service'));

            $repo_manager = new \Gap\Repo\RepoManager($this->config->get('repo'));
            $db_manager = new \Gap\Database\DatabaseManager($this->config->get('db'));
            $repo_manager->setDbManager($db_manager);

            $dto_manager = new \Gap\Dto\DtoManager($this->config->get('dto'));
            $repo_manager->setDtoManager($dto_manager);

            if ($cache_config = $this->config->get('cache')) {
                $cache_manager = new \Gap\Cache\CacheManager($cache_config);
                $repo_manager->setCacheManager($cache_manager);
            }

            $service_manager->setRepoManager($repo_manager);
            $this->service_manager = $service_manager;
        }

        return $this->service_manager;
    }

    public function getResponse($rs)
    {
        if ($rs instanceof Response) {
            return $rs;
        } else if (is_string($rs)) {
            return new Response($rs);
        } else if (is_array($rs)) {
            return new JsonResponse($rs);
        } else {
            var_dump($rs);
            _debug('todo response type incorrect');
        }
    }

    public function isAccess($access, $user)
    {
        return true;
    }

    protected function protectCsf(Request $request)
    {
        if ($request->isMethod('POST')) {
            if ($request->request->get('_token') !== session()->get('_token')) {
                return false;
            }
        }

        return true;
    }
}
