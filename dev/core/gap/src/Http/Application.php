<?php

namespace Gap\Http;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Gap\Http\Exception\CsrfException;
use Gap\Routing\Exception\NotLoginException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Gap\Core\ApplicationCore;

class Application extends ApplicationCore implements HttpKernelInterface
{
    protected $http_request = '';

    protected $router;

    public function getHttpRequest()
    {
        if ($this->http_request) {
            return $this->http_request;
        }

        $this->http_request = \Gap\Http\Request::createFromGlobals();
        return $this->http_request;
    }

    public function init($opts = [])
    {
        $this->base_dir = $opts['base_dir'];
        $this->loader = $opts['loader'];

        $this->config = id(new \Gap\Core\ConfigProvider($this->base_dir))->make();
        $this->router = id(new \Gap\Core\RouterProvider($this->config))->make();
        $this->initLoader(
            id(new \Gap\Core\AutoloadProvider($this->config))->make()
        );

        if ($this->debug) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
        parent::init($opts);
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function run()
    {
        $response = $this->handle($this->getHttpRequest());
        $response->send();
        //todo
        //$this->terminate($request, $response);
    }

    public function handle(SymfonyRequest $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if (!$this->checkCsrf($request)) {
            if ($referer = $request->headers->get('referer')) {
                return $this->gotoUrlResponse($referer);
            }

            return $this->csrfErrorResponse();
        }

        $host = $request->getHttpHost();
        $parsed = $this->parsePathInfo($request->getPathInfo());
        $path = $parsed['path'];
        $locale_key = $parsed['locale_key'];

        if (!$locale_key) {
            if ($path !== '/') {
                return $this->localeNotFoundResponse();
            }

            return $this->gotoLocaleResponse($request);
        }

        if (!$path) {
            return $this->gotoLocaleResponse($request);
        }

        translator()->setLocaleKey($locale_key);
        $this->router->setCurrentLocaleKey($locale_key);
        $route = $this->router->dispatch($request->getMethod(), $host, $path);
        if ($route[0] !== 1) {
            return $this->routeNotFoundResponse();
        }

        return $this->routeResponse($route, $request);
    }

    //
    // sc-protected-functions
    //

    protected function checkCsrf($request)
    {
        if ($request->isMethod('POST')) {
            if ($request->request->get('_token') !== session()->get('_token')) {
                return false;
                //throw new CsrfException('login required');
            }
            if ($request->request->get('_is_refresh_token')) {
                session_refresh_token();
            }
        }

        return true;
    }

    protected function getViewEngine($app_name)
    {
        if (!$app_dir = $this->config->get("app.$app_name.dir")) {
            _debug("cannot find app: $app_name");
        }

        $folders = [$app_dir . '/view'];
        $foil = \Foil\Foil::boot(
            [
                'folders' => $folders,
                'autoescape' => false,
                'ext' => 'phtml',
            ]
        );
        $view_engine = $foil->engine();

        return $view_engine;
    }

    protected function parsePathInfo($pathinfo)
    {
        $locale_config = $this->config->get('i18n.locale');

        $path = substr($pathinfo, 1);
        $pos = strpos($path, '/');
        if ($pos !== false) {
            $try_locale_key = substr($path, 0, $pos);
            if ($locale = $locale_config->get('available.'.$try_locale_key)) {
                $pathinfo = substr($pathinfo, $pos + 1);

                return [
                    'locale_key' => $try_locale_key,
                    'path' => $pathinfo,
                ];
            }
        }
        if ($locale = $locale_config->get('available.' . $path)) {
            return [
                'locale_key' => $path,
                'path' => '',
            ];
        }

        return ['locale_key' => '', 'path' => $pathinfo];
    }

    protected function getLocaleUrl($host, $locale_keys, $is_secure = false)
    {
        $locale_config = $this->config->get('i18n.locale.available');
        $locale_url = '';
        $protocol = $is_secure ? 'https://' : 'http://';
        foreach ($locale_keys as $locale_key) {
            $locale_key = str_replace('_', '-', strtolower($locale_key));
            if ($locale_config->get($locale_key)) {
                $locale_url = $protocol . $host . '/' . $locale_key . '/';
                break;
            }
        }

        return $locale_url;
    }

    //
    // sc-response ---
    //
    protected function deniedResponse()
    {
        $response = new Response('Forbidden');
        $response->setStatusCode(403);

        return $response;
    }

    protected function csrfErrorResponse()
    {
        $response = new Response('csrf error');
        $response->setStatusCode(400);

        return $response;
    }

    protected function localeNotFoundResponse()
    {
        $response = new Response('locale not found');
        $response->setStatusCode(404);

        return $response;
    }

    protected function routeNotFoundResponse()
    {
        $response = new Response('route not found');
        $response->setStatusCode(404);

        return $response;
    }

    protected function gotoUrlResponse($url)
    {
        return new RedirectResponse($url);
    }

    protected function toLoginRedirectResponse($target_url = '')
    {
        session()->set('target_url', $target_url);
        $url = $this->router->getUrlByRoute('login');

        return new RedirectResponse($url);
    }

    protected function routeResponse($route, $request)
    {
        // public
        $access = prop($route[1], 'access', 'public');
        if ($access == 'public') {
            return $this->actionResponse($route, $request);
        }

        // need uid
        $uid = current_uid();
        if (!$uid) {
            return $this->toLoginRedirectResponse($request->getUri());
        }

        if ($access == 'login') {
            return $this->actionResponse($route, $request);
        }

        // need user
        $is_allowed = false;

        if ($user = current_user()) {
            $require_roles = prop($route[1], 'roles');
            $access_privilege = (int) $this->config->get('privilege.'.$access);

            if ($user->privilege > $access_privilege) {
                $is_allowed = true;
            } elseif ($user->privilege == $access_privilege) {
                $require_roles = prop($route[1], 'roles');
                if (!$require_roles) {
                    $is_allowed = true;
                } elseif ($user->hasRole($require_roles)) {
                    $is_allowed = true;
                }
            }
        }

        if ($is_allowed) {
            return $this->actionResponse($route, $request);
        } else {
            return $this->deniedResponse();
        }
    }

    protected function actionResponse($route, $request)
    {
        $handle = $route[1];
        $params = $route[2];

        $action = prop($handle, 'action');
        $as = prop($handle, 'as');
        $mode = prop($handle, 'mode', 'ui');
        $access = prop($handle, 'access');
        $roles = prop($handle, 'roles');
        $app_name = prop($handle, 'app');

        list($class, $fun) = explode('@', $action);
        /*
        if ($mode === 'rest') {
            $class = '\\Tos\\Rest\\'.$class;
        } else {
            $class = '\\Tos\\Ui\\'.$class;
        }
         */
        $class = '\\' . $class;
        $controller = new $class($request);
        $controller->setParams($params);
        $controller->setApp($this);

        $view_engine = $this->getViewEngine($app_name);
        $view_engine->useData([
            'config' => $this->config,
            'request' => $request,
        ]);
        $controller->setViewEngine($view_engine);

        $rs = $controller->bootstrap();

        if (!$rs) {
            $rs = $controller->$fun();
        }

        if ($rs instanceof Response) {
            return $rs;
        }

        if (is_string($rs)) {
            return new Response($rs);
        }

        if (is_array($rs)) {
            return new JsonResponse($rs);
        }

        if ($rs instanceof \Gap\Pack\PackDto) {
            return new JsonResponse($rs->toArray());
        }

        _debug('todo repose type unknown');
    }

    protected function gotoLocaleResponse($request)
    {
        if ($locale_url = $this->getLocaleUrl($request->getHttpHost(), $request->getLanguages(), $request->isSecure())) {
            return $this->gotoUrlResponse($locale_url);
        }

        return $this->localeNotFoundResponse();
    }
}
