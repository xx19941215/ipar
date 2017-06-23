<?php

function id($x)
{
    return $x;
}

function app()
{
    static $app;
    if ($app) {
        return $app;
    }

    $app = \Gap\Application::app();
    return $app;
}

function config()
{
    static $config;

    if ($config) {
        return $config;
    }

    $config = app()->getConfig();

    return $config;
}

function http_request()
{
    static $http_request;
    if ($http_request) {
        return $http_request;
    }

    $http_request = app()->getHttpRequest();
    return $http_request;
}

function router()
{
    static $router;

    if ($router) {
        return $router;
    }

    $router = app()->getRouter();

    return $router;
}

function route_url($name, $params = [], $query = [], $protocol = 'http://')
{
    return router()->getUrlByRoute($name, $params, $protocol).($query ? ('?'.http_build_query($query)) : '');
}

function static_url($uri, $protocol = 'http://')
{
    static $site_static_host;
    if (!$site_static_host) {
        $site_static_host = config()->get('site.static.host');
    }
    if (!$uri) {
        $uri = '';
    }

    if ($uri[0] !== '/') {
        $uri = '/'.$uri;
    }

    return $protocol.$site_static_host.$uri;
}

function nos_url($uri, $dir = '/img' , $protocol = 'http://', $nid = 'ipar-static')
{
    static $site_nos_host;
    if (!$site_nos_host) {
        $site_nos_host = config()->get('site.nos.host');
    }
    if (!$uri) {
        $uri = '';
    }

    if ($uri[0] !== '/') {
        $uri = '/'.$uri;
    }

    return $protocol . $nid . '.' . $site_nos_host . $dir . $uri;
}
function img_url($uri, $protocol = 'http://')
{
    return static_url('img/' . $uri, $protocol);
    /*
    static $img_url_base;
    if (!$img_url_base) {
        $img_url_base = config()->get('site.static.host').'/img';
    }
    if ($uri[0] !== '/') {
        $uri = '/'.$uri;
    }

    return $protocol.$img_url_base.$uri;
     */
}
function css_url($uri, $protocol = 'http://')
{
    return static_url('css/' . $uri, $protocol);

    /*
    static $css_url_base;
    if (!$css_url_base) {
        $css_url_base = config()->get('site.static.host').'/css';
    }
    if ($uri[0] !== '/') {
        $uri = '/'.$uri;
    }

    return $protocol.$css_url_base.$uri;
     */
}
function js_url($uri, $protocol = 'http://')
{
    static $js_url_base;
    if (!$js_url_base) {
        $config = config();
        $host_static = $config->get('site.static.host');
        $js_url_base = $host_static.'/js';
        /*
        if ($config->get('debug')) {
            $js_url_base = $host_static . '/js/dev';
        } else {
            $js_url_base = $host_static . '/js/dist';
        }
         */
    }
    if ($uri[0] !== '/') {
        $uri = '/'.$uri;
    }

    return $protocol.$js_url_base.$uri;
}
function trace()
{
    static $trace;
    if ($trace) {
        return $trace;
    }

    $config = config();
    $trace = new \Gap\Trace\Trace(
        $config->get('db.default'),
        $config->get('trace.level'),
        $config->get('trace.index')
    );
    return $trace;
}

function locale_set()
{
    static $locale_set;
    if ($locale_set) {
        return $locale_set;
    }

    $locale_set = new \Gap\I18n\LocaleSet(config()->get('i18n.locale.available')->all());
    return $locale_set;
}

function translator()
{
    static $translator;
    if (!$translator) {
        $config = config();
        $translator = new \Gap\I18n\Translator(
            $config->get('db.i18n'),
            $config->get('cache.i18n'),
            locale_set(),
            //$config->get('i18n.locale.available')->all(),
            $config->get('i18n.translator.db_table'),
            $config->get('i18n.locale.default')
        );
    }

    return $translator;
}

function trans(...$args)
{
    if (is_array($args[0])) {
        $args = $args[0];
    }
    if ($translator = translator()) {
        $args[0] = $translator->translate($args[0]);
    }
    if (isset($args[1])) {
        return call_user_func_array('sprintf', $args);
    }

    return $args[0];
}

function dto_manager()
{
    static $dto_manager;
    if (!$dto_manager) {
        $dto_manager = new \Gap\Dto\DtoManager(config()->get('dto'));
    }

    return $dto_manager;
}

function gap_repo_manager($name = 'default')
{
    return repo_manager($name);
}

function repo_manager($name = 'default')
{
    static $repo_managers = [];
    if (isset($repo_managers[$name])) {
        return $repo_managers[$name];
    }

    $config = config()->get('repo_manager')->get($name);
    $db = db_manager()->get($config->get('db'));
    $repo_managers[$name] = new \Gap\Repo\RepoManager($db, config()->get('repo'));

    return $repo_managers[$name];
}

function gap_service_manager()
{
    return service_manager();
}

function service_manager()
{
    static $gap_service_manager;
    if ($gap_service_manager) {
        return $gap_service_manager;
    }

    $gap_service_manager = new \Gap\Service\ServiceManager(config()->get('service'));
    return $gap_service_manager;
}


function service($service_name)
{
    return service_manager()->make($service_name);
}

function adapter_manager()
{
    static $adapter_manager;
    if (!$adapter_manager) {
        $adapter_manager = new \Gap\Database\AdapterManager(config()->get('db'));
    }

    return $adapter_manager;
}
// deprecated
function db_manager()
{
    return adapter_manager();
}

function cache_manager()
{
    static $cache_manager;
    if (!$cache_manager) {
        $cache_manager = new \Gap\Cache\CacheManager(config()->get('cache'));
    }

    return $cache_manager;
}

function qrcode_url($code, $sub_dir = 'default')
{
    $hash = md5($code);
    $path = '/'.$sub_dir.'/'.$hash;
    $base_dir = config()->get('dir.static').'/qrcode';
    $png = $base_dir.'/'.$path.'.png';
    if (!file_exists($png)) {
        $dir = $base_dir.'/'.$sub_dir;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $renderer = new \BaconQrCode\Renderer\Image\Png();
        $renderer->setHeight(200);
        $renderer->setWidth(200);
        $writer = new \BaconQrCode\Writer($renderer);
        $writer->writeFile($code, $png);
    }

    return static_url('/qrcode'.$path.'.png');
}
function img_src($img, $size = '')
{
    if (!isset($img['dir']) || !$img['dir']) {
        return static_url('error.png');
    }

    $site = $img['site'];
    $size_remote = config()->get("img.size.$size");
    $dir_url = static_url($img['dir']);

    if ($site == 'static') {
        if ($size) {
            return "{$dir_url}/${img['name']}-{$size}.{$img['ext']}";
        }
        return "{$dir_url}/${img['name']}.{$img['ext']}";
    } elseif ($site == 'nos_ipar_upload') {
        if ($img['ext'] == 'gif') {
            $size_remote = '';
        }
        $host = config()->get('site.nos_ipar_upload.host');
        $img_url = "http://{$host}{$img['dir']}/{$img['name']}.{$img['ext']}{$size_remote}";
        return $img_url;
    } elseif ($site == 'nos_ipar_avt') {
        $host = config()->get('site.nos_ipar_avt.host');
        $img_url = "http://{$host}{$img['dir']}/{$img['name']}.{$img['ext']}{$size_remote}";
        return $img_url;
    }
}

function assert_mine($entity, $msg = 'no-permission')
{
    if ($entity->uid != current_uid()) {
        throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException($msg);
    }
}

function session()
{
    static $session;
    if ($session) {
        return $session;
    }

    $session = new \Gap\Http\Session\Session(config()->get('session')->all());
    $session->start();

    return $session;
}

function session_refresh_token()
{
    session()->set('_token', md5('milky.way'.time()));
}
function user_service()
{
    static $user_service;
    if ($user_service) {
        return $user_service;
    }
    $user_service = gap_service_manager()->make('user');
    return $user_service;
}

function var2file($target_path, $var)
{
    file_put_contents(
        $target_path,
        '<?php return ' . var_export($var, true) . ';'
    );
    //chmod($target_path, 0777);
}

function symfony_session()
{
    $session_config = config()->get('session');
    $save_path = $session_config->get('save_path');

    $save_handler = ('files' === $session_config->get('save_handler')) ?
        new \Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler($save_path)
        :
        new \Gap\Http\Session\Storage\Handler\NativeRedisSessionHandler($save_path);

    $storage = new \Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage(
        [
            'cookie_path' => '/',
            'cookie_domain' => $session_config->get('cookie_domain'),
            'name' => $session_config->get('name'),
            'cookie_lifetime' => $session_config->get('cookie_lifetime', 0),
        ],
        $save_handler
    );
    $session = new \Symfony\Component\HttpFoundation\Session\Session($storage);

    $session->start();
    if (!$session->has('_token')) {
        $session->set('_token', md5('milky.way'.time()));
    }
}

function current_uid()
{
    static $current_uid;
    if ($current_uid) {
        return $current_uid;
    }

    $current_uid = session()->get('uid');
    return (int) $current_uid;

    /*
     * old todelete later
    $user_service = user_service();
    if ($current_uid = $user_service->getCurrentUid()) {
        return $current_uid;
    }
    if ($session_uid = session()->get('uid')) {
        $user_service->setCurrentUid($session_uid);
        return $session_uid;
    }
    return 0;
     */
}
function set_current_uid($uid)
{
    session()->set('uid', $uid);
}
function current_user()
{
    static $current_user;
    if ($current_user) {
        return $current_user;
    }

    if ($current_uid = current_uid()) {
        $current_user = user_service()->getUserByUid($current_uid);
    }

    return $current_user;
    /*
     * todo checklater
    if (current_uid()) {
        return service('user')->getCurrentUser();
    }
    return null;
     */
}
function user($uid)
{
    return user_service()->getUserByUid($uid);
}

function time_elapsed_string($datetime)
{
    if (is_int($datetime)) {
        $datetime = date(DATE_ATOM, $datetime);
    }
    $datetime = new \DateTime($datetime);
    $now = new \DateTime();
    $diff = $now->diff($datetime);
    if ($diff->y) {
        return $datetime->format('Y-m-d');
        //return trans('%d-years-ago', $diff->y);
    }
    if ($diff->m) {
        return $datetime->format('Y-m-d');
        //return trans('%d-months-ago', $diff->m);
    }
    if ($diff->d) {
        return trans('%d-days-ago', $diff->d);
    }
    if ($diff->h) {
        return trans('%d-hours-ago', $diff->h);
    }
    if ($diff->i) {
        return trans('%d-minutes-ago', $diff->i);
    }
    if ($diff->s) {
        return trans('%d-seconds-ago', $diff->s);
    }

    return trans('just-now');
}
function is_mine($entity)
{
    if ($current_user = current_user()) {
        return $entity && isset($entity->uid) && $entity->uid == $current_user->uid;
    }

    return false;
}
//-------
function prop($arr, $key, $default = '')
{
    if (is_array($arr)) {
        return array_key_exists($key, $arr) ? $arr[$key] : $default;
    }
    return $default;
}

function route_redirect($route, $params = [], $protocol = 'http://', $status = 302)
{
    $url = router()->getUrlByRoute($route, $params, $protocol);

    return new \Symfony\Component\HttpFoundation\RedirectResponse($url, $status);
}

function dto_encode($dto)
{
    return json_encode($dto);
}
function arr2dto($arr, $type_key = '')
{
    $type_key = $type_key ? $type_key : get_type_key($arr['type_id']);
    $dto_class = dto_manager()->get($type_key);
    if (!$dto_class) {
        return null;
        _debug('todo cannot find type: '.$type_key);
    }

    $dto = new $dto_class();
    if (is_array($arr)) {
        foreach ($arr as $key => $val) {
            if (property_exists($dto, $key)) {
                $dto->$key = $val;
            }
        }
    }

    return $dto;
}

function _debug($msg)
{
    echo "$msg \n";
    debug_print_backtrace();
}

function dto_decode($str, $type_key)
{
    $dto_class = dto_manager()->get($type_key);
    if (!$dto_class) {
        _debug('todo cannot find type: ' . $type_key);
    }
    $dto = new $dto_class();
    $arr = json_decode($str, true);
    if (is_array($arr)) {
        foreach ($arr as $key => $val) {
            if (property_exists($dto, $key)) {
                $dto->$key = $val;
            }
        }
    }
    return $dto;
}

function entity_encode($entity)
{
    return json_encode(['type_id' => $entity->type_id, 'entity' => $entity]);
}
function entity_decode($str)
{
    $arr = json_decode($str, true);
    $type_key = get_type_key($arr['type_key']);
    $entity_arr = $arr['entity'];
    $class = dto_manager()->get($type_key);
    if (!$class) {
        _debug('todo cannot find entity type: '.$type_key);
    }
    $entity = new $class();
    foreach ($entity_arr as $key => $val) {
        if (property_exists($entity, $key)) {
            $entity->$key = $val;
        }
    }

    return $entity;
}

function current_date()
{
    return date('Y-m-d H:i:s', time());
}

function current_micro_date()
{
    $now = microtime();
    list($usec, $sec) = explode(' ', $now);
    $usec = substr(str_replace('0.', '.', $usec), 0, 7);
    return date('Y-m-d H:i:s', $sec) . $usec;
}

function parse_time($time)
{
    return date('Y-m-d H:i:s', $time);
}

function parse_microtime($mirotime)
{
    list($usec, $sec) = explode(' ', $mirotime);
    $usec = substr(str_replace('0.', '.', $usec), 0, 7);
    return date('Y-m-d H:i:s', $sec) . $usec;
}
function type_set()
{
    static $type_set;
    if ($type_set) {
        return $type_set;
    }

    $type_set = new \Gap\Set\TypeSet(
        config()->get('type')->all()
    );
    return $type_set;
}
function get_type_id($type_key)
{
    return type_set()->getId($type_key);
}

function get_type_key($type_id)
{
    return type_set()->getKey($type_id);
}

function get_action_id($action_key)
{
    return action_set()->getId($action_key);
}

function get_action_key($action_id)
{
    return action_set()->getKey($action_id);
}

function action_set()
{
    static $action_set;
    if ($action_set) {
        return $action_set;
    }

    $action_set = new \Gap\Set\KeyIdSet(
        config()->get('action')->all()
    );
    return $action_set;
}


function csrf_field()
{
    return implode([
        '<input type="hidden" name="_token" value="',
        session()->get('_token'),
        '">',
        '<input type="hidden" name="_is_refresh_token" value="1">']);
}

$Logs = [];
function add_log($log)
{
    global $Logs;
    if (is_string($log)) {
        $Logs[] = $log;
        return;
    }

    if (is_array($log)) {
        foreach ($log as $key => $val) {
            $Logs[] = " - $key: $val";
        }
        return;
    }

}
function echo_logs()
{
    global $Logs;
    echo implode("\n", $Logs);
    echo "\n";
}

function image_tool()
{
    static $img_tool;
    if ($img_tool) {
        return $img_tool;
    }

    $config = config();
    $site = $config->get('img.site');
    $img_tool = new \Gap\File\ImageTool(
        $config->get("site.$site.dir"),
        $config->get('img.base_dir')
    );
    return $img_tool;
}

/*
function section_item_title($title)
{
    if (mb_strlen($title) > 42) {
        $title = mb_substr($title, 0, 40) . ' ...';
    }
    return $title;
}
 */

function title_text($title, $length = 40)
{
    if (mb_strlen($title) > $length) {
        $title = mb_substr($title, 0, $length) . ' ...';
    }
    return $title;
}

/*
    public function __construct($data = [])
    {
        if ($data) {
            foreach ($data as $key => $val) {
                if (property_exists($this, $key)) {
                    $this->$key = $val;
                }
            }
        }
    }

 */

// maybe delete -------

/*
function session () {
    static $sess;
    if (!$sess) {
        $config = config();
        $savePath = $config->get('session.save_path');
        $storage = new \Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage(
            [
                'cookie_path' => '/',
                'cookie_domain' => $config->get('session.cookie_domain'),
                'name' => $config->get('session.name'),
                'cookie_lifetime' => $config->get('session.cookie_lifetime'),
            ],
            new \Gap\Http\Session\Storage\Handler\NativeRedisSessionHandler($savePath)
        );
        $sess = new \Symfony\Component\HttpFoundation\Session\Session($storage);
        $sess->start();
        if (!$sess->has('_token')) {
            $sess->set('_token', password_hash(uniqid(time()), PASSWORD_DEFAULT));
        }
        $sess;
    }
    return $sess;
}
*/
/*
function dbs() {
    static $dbs;
    if (!$dbs) {
        $dbs = new \Gap\Database\DatabaseContainer();
    }
    return $dbs;
}
 */
/*
function caches() {
    static $caches;
    if (!$caches) {
        $caches = new \Gap\Cache\CacheContainer();
    }
    return $caches;
}
 */
/*
function bc() {
    static $bc;
    if (!$bc) {
        $bc = new \Gap\Business\BusinessContainer();
        $bc
            ->setConfig(config())
            ->setDbs(dbs())
            ->setCaches(caches())
            ->setSession(session());
    }
    return $bc;
}
function bs($id) {
    return bc()->get($id);
}

function acl() {
    static $acl;
    if (!$acl) {
        $acl = new \Gap\Security\Acl();
    }
    return $acl;
}
function save_img($file) {
    $config = config();
    $img_base_url = $config->get('img.base_url');
    $img_base_path = $config->get('img.base_path');
    $relative_path = 'img/' . date('Y/m/d');

    $img_dir_url = $img_base_url . '/' . $relative_path;
    $img_dir_path = $img_base_path . '/' .$relative_path;

    if (!is_dir($img_dir_path)) {
        mkdir($img_dir_path, 0777, true);
    }

    $ext = strtolower($file->getClientOriginalExtension());
    $img_base_name = uniqid('');
    $img_name = $img_base_name . '.' . $ext;

    $file->move($img_dir_path, $img_name);

    return [
        'url' => $img_dir_url . '/' . $img_name,
        'path' => $img_dir_path . '/' . $img_name,
        'dir_url' => $img_dir_url,
        'dir_path' => $img_dir_path,
        'base' => $img_base_name,
        'ext' => $ext,
    ];

}
 */
