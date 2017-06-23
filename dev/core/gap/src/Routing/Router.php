<?php
namespace Gap\Routing;

use Gap\Tool\FileCompilerTrait;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

use FastRoute\RouteParser\Std as RouteParser;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\DataGenerator\GroupCountBased as DataGenertor;

class Router {

    use FileCompilerTrait;

    private $route_parser;

    private $dispatchers;
    private $data_generators;

    private $current_access = 'public';
    private $current_app = '';
    private $current_pathinfo = '';
    private $current_host = '';

    private $route_maps;
    private $action_maps;

    protected $current_locale_key;

    protected $current_site;
    protected $host_maps;
    protected $site_maps;

    protected $includes = [];

    protected $routes = [];


    public function addSite($site, $host)
    {
        $this->host_maps[$site] = $host;
        $this->site_maps[$host] = $site;
        return $this;
    }

    public function getHost($site) {
        return isset($this->host_maps[$site]) ? $this->host_maps[$site] : '';
    }

    public function getSite($host) {
        return isset($this->site_maps[$host]) ? $this->site_maps[$host] : '';
    }
    public function setCurrentSite($site) {
        $this->current_site = $site;
        return $this;
    }
    public function setCurrentAccess($access) {
        $this->current_access = $access;
        return $this;
    }
    public function setCurrentApp($app) {
        $this->current_app = $app;
        return $this;
    }

    public function addRoute($http_method, $pattern, $handler) {
        if (!isset($handler['as'])) {
            _debug('route as cannot be empty');
                return;
        }

        if (!isset($handler['access'])) {
            $handler['access'] = $this->current_access ? $this->current_access : 'public';
        }
        if (!isset($handler['app'])) {
            if (!$this->current_app) {
                _debug('router current_app cannot be empty');
                return;
            }

            $handler['app'] = $this->current_app;
        }
        if (!isset($handler['site'])) {
            if (!$this->current_site) {
                _debug('router current_site cannot be empty');
                return;
            }

            $handler['site'] = $this->current_site;
        }

        if (!isset($handler['mode'])) {
            $handler['mode'] = 'ui';
        }

        $route_datas = $this->getRouteParser()->parse($pattern);
        $data_generator = $this->getDataGenerator($handler['site']);

        $this->routes[$handler['as']] = [
            'http_method' => $http_method,
            'pattern' => $pattern,
            'handler' => $handler
        ];

        foreach ((array) $http_method as $method) {
            foreach ($route_datas as $route_data) {
                $data_generator->addRoute($method, $route_data, $handler);
                if (isset($handler['as'])) {
                    $this->route_maps[$handler['as']] = array($this->current_site, $pattern, prop($handler, 'mode', 'ui'));
                }
                $this->action_maps[$handler['action']] = array($this->current_site, $pattern);
            }
        }
    }

    public function get($pattern, $handler) {
        $this->addRoute('GET', $pattern, $handler);
        return $this;
    }

    public function post($pattern, $handler) {
        $this->addRoute('POST', $pattern, $handler);
        return $this;
    }

    public function match($http_methods, $pattern, $handler) {
        foreach ($http_methods as $http_method) {
            $this->addRoute($http_method, $pattern, $handler);
        }
        return $this;
    }

    public function getRest($pattern, $handler) {
        $handler['mode'] = 'rest';
        $this->addRoute('GET', $pattern, $handler);

        return $this;
    }

    public function postRest($pattern, $handler) {
        $handler['mode'] = 'rest';
        $this->addRoute('POST', $pattern, $handler);
        return $this;
    }


    public function getUrlByRoute($route_name, $parameters = [], $protocol = 'http://') {
        if (isset($this->route_maps[$route_name])) {
            //$route = $this->route_maps[$route_name];
            //list($site, $path) = explode(':', $route);
            list($site, $pattern) = $this->route_maps[$route_name];
            $host = $this->getHost($site);
            return $protocol . $host . '/' . $this->replaceRouteParameters($this->current_locale_key . $pattern, $parameters);
        }
        return '';
    }

    public function getI18nUrls($protocol = 'http://')
    {
        $urls;
        foreach (config()->get('i18n.locale.enabled')->all() as $locale_key) {
            $urls[$locale_key] = $protocol . $this->current_host . '/' . $locale_key . $this->current_pathinfo;
        }
        return $urls;
    }
    /*
    public function getUrlByAction($action, $parameters = []) {
        if (isset($this->action_maps[$action])) {
            $route = $this->action_maps[$action];
            return $protocol . $this->replaceRouteParameters($route, $parameters);
        }
        return '';
    }
     */
    public function dispatch($method, $host, $pathinfo)
    {
        $site = $this->getSite($host);
        $dispatcher = $this->getDispatcher($site);

        $route = $dispatcher->dispatch(
            $method,
            $pathinfo
        );

        $this->current_host = $host;
        $this->current_pathinfo = $pathinfo;
        $this->current_route = $route;

        return $route;

        /*
        switch ($route[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                throw new NotFoundHttpException();
                break;

            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedHttpException();
                break;

            case \FastRoute\Dispatcher::FOUND:
                return $route;
                break;
        }
         */
    }

    public function getCurrentRoute()
    {
        return $this->current_route;
    }

    public function getCurrentRouteName()
    {
        return $this->current_route[1]['as'];
    }

    public function setCurrentLocaleKey($locale_key)
    {
        $this->current_locale_key = $locale_key;
        return $this;
    }

    public function getCurrentLocaleKey()
    {
        return $this->current_locale_key;
    }

    public function getRestRoutes()
    {
        foreach ($this->route_maps as $route => $args) {
            list($site, $pattern, $mode) = $args;
            $protocol = 'http://';
            $parameters = [];
            if ($mode === 'rest') {
                $host = $this->getHost($site);
                $routes[$route] = [
                    'host' => $this->getHost($site),
                    'pattern' => $pattern
                ];
            }
        }

        return $routes;
    }

    public function getAllRoutes()
    {
        return $this->routes;
    }

    public function load($routes)
    {
        foreach ($routes as $route) {
            $this->addRoute(
                $route['http_method'],
                $route['pattern'],
                $route['handler']
            );
        }
    }

    public function clear()
    {
        $this->route_parser = null;

        $this->dispatchers = null;
        $this->data_generators = null;

        $this->current_access = 'public';
        $this->current_app = '';
        $this->current_pathinfo = '';
        $this->current_host = '';

        $this->route_maps = [];
        $this->action_maps = [];
        //$this>$current_locale_key = '';

        $this->current_site = '';
        $this->host_maps = [];
        $this->site_maps = [];

        $this->includes = [];

        $this->routes = [];
    }

    protected function replaceRouteParameters($path, array &$parameters) {
        if (count($parameters)) {
            $path = $this->preg_replace_sub(
                '/\{.*?\}/', $parameters, $this->replaceNamedParameters($path, $parameters)
            );
        }

        return preg_replace('/\{.*?\?\}/', '', $path);
        //return trim(preg_replace('/\{.*?\?\}/', '', $path), '/');
    }

    protected function replaceNamedParameters($path, &$parameters) {
        return preg_replace_callback('/\{(.*?)\??\}/', function ($m) use (&$parameters) {
            return isset($parameters[$m[1]]) ? Arr::pull($parameters, $m[1]) : $m[0];

        }, $path);
    }

    protected function preg_replace_sub ($pattern, &$replacements, $subject) {
        return preg_replace_callback($pattern, function($match) use (&$replacements)  {
            return array_shift($replacements);
        }, $subject);
    }

    protected function getRouteParser()
    {
        if (!$this->route_parser) {
            $this->route_parser = new RouteParser();
        }
        return $this->route_parser;
    }

    protected function getDispatcher($site)
    {
        if (!isset($this->dispatchers[$site])) {
            if ($data_generator = $this->getDataGenerator($site)) {
                $this->dispatchers[$site] = new Dispatcher($data_generator->getData());
            } else {
                return null;
            }
        }
        return $this->dispatchers[$site];
    }
    protected function getDataGenerator($site)
    {
        if (!isset($this->data_generators[$site])) {
            $this->data_generators[$site] = new \FastRoute\DataGenerator\GroupCountBased();
        }
        return $this->data_generators[$site];
    }

}
