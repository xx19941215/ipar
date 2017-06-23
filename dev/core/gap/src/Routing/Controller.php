<?php
namespace Gap\Routing;
use Symfony\Component\HttpFoundation\Request;
use Mars\PackTrait;

class Controller {

    use \Gap\Pack\PackTrait;

    protected $request;
    protected $params;
    protected $view_engine;
    protected $app;
    protected $config;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function setApp($app) {
        $this->app = $app;
        return $this;
    }
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    public function setParams($params) {
        $this->params = $params;
        return $this;
    }

    public function getParam($name) {
      //print_R($this->params);exit;
        return isset($this->params[$name]) ? $this->params[$name] : '';
    }

    public function badRequest($msg = '') {
        throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException($msg);
    }

    public function bootstrap() {
    }

    public function setViewEngine($view_engine) {
        $this->view_engine = $view_engine;
        return $this;
    }

    public function view($tpl, $data = []) {
        return $this->view_engine->render($tpl, $data);
    }

    public function page($page_tpl, $data = [])
    {
        return $this->view_engine->render('page/' . $page_tpl, $data);
    }

    public function service($service_name) {
        return service_manager()->make($service_name);
    }

    protected function gotoRoute($name, $params = [], $query = [], $status = 302, $protocol = 'http://') {
        $url = router()->getUrlByRoute($name, $params, $protocol) . ($query ? ('?' . http_build_query($query)) : '');
        return $this->gotoUrl($url, $status);
    }

    protected function gotoUrl($url, $status = 302) {
        return new \Symfony\Component\HttpFoundation\RedirectResponse($url, $status);
    }

    protected function setTargetUrl($target_url) {
        session()->set('target_url', $target_url);
    }

    protected function gotoTargetUrl($target_url = '', $default_url = '', $status = 302) {
        if ($target_url) {
            return $this->gotoUrl($target_url, $status);
        }
        if ($target_query = $this->request->query->get('target', '')) {
            return $this->gotoUrl($target_query, $status);
        }

        if ($target_session = session()->get('target_url')) {
            return $this->gotoUrl($target_session, $status);
        }

        if ($default_url) {
            return $this->gotoUrl($default_url, $status);
        }

        return $this->gotoRoute('home', [], [], $status);

    }

    protected function referTargetUrl()
    {
        if ($referer = $this->request->headers->get('referer')) {
            $this->setTargetUrl($referer);
        }
    }

    protected function prepareTargetUrl($target_url = '')
    {
        if ($target_url) {
            session()->set('target_url', $target_url);
            return;
        }

        if ($this->request->query->get('t', '')) {
            return;
        }

        if ($target_query = $this->request->query->get('target', '')) {
            session()->set('target_url', $target_query);
            return;
        }

        if ($target_request = $this->request->request->get('target', '')) {
            session()->set('target_url', $target_request);
            return;
        }

        if ($referer = $this->request->headers->get('referer')) {
            session()->set('target_url', $referer);
            return;
        }
    }
}
