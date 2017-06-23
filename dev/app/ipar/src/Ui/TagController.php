<?php
namespace Ipar\Ui;


class TagController extends IparControllerBase
{
    public function index()
    {
        $tag = $this->getTagByParam();
        return $this->page('tag/index-all', ['tag' => $tag, 'type' => 'tag_all']);
    }

    protected function getTagByParam()
    {
        $zcode = $this->getParam('zcode');
        return $this->service('tag')->getTagByZcode($zcode);
    }

}