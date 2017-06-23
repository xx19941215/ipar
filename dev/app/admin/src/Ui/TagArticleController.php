<?php

namespace Admin\Ui;

class TagArticleController extends AdminControllerBase
{

    public function addTagMultiple()
    {
        $url = $_SERVER['HTTP_REFERER'];
        $article = $this->getArticleFromParam();
        return $this->page('article/add-tag-multiple', ['article' => $article, 'url' => $url]);
    }

    public function addTagMultiplePost()
    {
        $article = $this->getArticleFromParam();
        $data['uid'] = current_uid();
        $data['article_id'] = $article->id;
        $url = $this->request->request->get('url');
        $data['titles'] = $this->request->request->get('multiple_title');
        $pack = $this->service('article_tag')->saveMultipleTag($data);
        if (!$pack->isOk()) {
            return $this->page('article/add-tag-multiple', [
                'article' => $article,
                'tag' => arr2dto($data, 'tag'),
                'errors' => $pack->getErrors()
            ]);
        }

        return $this->gotoTargetUrl($url);
    }

    public function showArticle()
    {
        $page = $this->request->query->get('page');
        $tag_id = $this->getParam('tag_id');
        $tag = $this->service('tag')->findOne($tag_id);
        $article_set = $this->service('tag_article')->search(['tag_id'=>$tag_id])
            ->setCurrentPage($page);
        return $this->page('tag/article', ['tag' => $tag, 'article_set' => $article_set]);
    }

    protected function getArticleFromParam()
    {
        $article_zcode = $this->getParam('zcode');
        return $this->service('article')->getArticleByZcode($article_zcode);
    }
}