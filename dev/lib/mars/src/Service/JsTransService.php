<?php
namespace Mars\Service;

class JsTransService extends MarsServiceBase
{
    public function createJsTrans($data = [])
    {
        $create = $this->repo('js_trans')->createJsTrans($data);
        if ($create->isOk()) {
            $this->cache()->delete('js-trans-key');
        }
        return $create;
    }

    public function getJsTranKeysFromDb()
    {
        return $this->repo('js_trans')->getJsTranKeysFromDb();
    }

    public function getJsTranKeys()
    {
        $json = $this->cache()->get('js-trans-key');
        if (!$json) {
            $items = $this->getJsTranKeysFromDb([]);
            $this->cache()->set('js-trans-key', json_encode($items));
            return $items;
        }

        $items = json_decode($json);
        return $items;
    }
}
