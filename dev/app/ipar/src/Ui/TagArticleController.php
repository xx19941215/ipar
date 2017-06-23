<?php
namespace Ipar\Ui;


class TagArticleController extends IparControllerBase
{
    public function index()
    {
        $tag = $this->getTagByParam();
        return $this->page('tag/index', ['tag' => $tag, 'type' => 'tag_article']);
    }

    protected function getTagByParam()
    {
        $zcode = $this->getParam('zcode');
        return $this->service('tag')->getTagByZcode($zcode);
    }
}