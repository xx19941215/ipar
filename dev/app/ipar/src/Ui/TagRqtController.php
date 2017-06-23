<?php
namespace Ipar\Ui;


class TagRqtController extends IparControllerBase
{
    public function index()
    {
        $tag = $this->getTagByParam();
        return $this->page('tag/index', ['tag' => $tag, 'type' => 'tag_rqt']);
    }

    protected function getTagByParam()
    {
        $zcode = $this->getParam('zcode');
        return $this->service('tag')->getTagByZcode($zcode);
    }
}