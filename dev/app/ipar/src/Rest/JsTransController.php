<?php
namespace Ipar\Rest;

class JsTransController extends \Gap\Routing\Controller
{
    public function jsTransPost()
    {
        $keys = $this->request->request->get('key');
        $create = $this->service('js_trans')->createJsTrans(['key' => $keys]);
        return $create;
    }
}
