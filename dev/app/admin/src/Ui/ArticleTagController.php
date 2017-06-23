<?php
namespace Admin\Ui;

class ArticleTagController extends AdminControllerBase
{
    protected $article_tag_service;

    public function bootstrap()
    {
        $this->article_tag_service = gap_service_manager()->make('article_tag');
    }


    public function search()
    {
        $article = $this->getArticleFromParam();
        $query = $this->request->query->get('query', '');
        $data['article_id'] = $article->id;
        $data['query']=$query;
        $tag_set = $this->article_tag_service->search($data);
        return $this->page('article/tag', [
            'article' => $article,
            'tag_set' => $tag_set,
        ]);
    }

    public function add()
    {
        $article = $this->getArticleFromParam();
        return $this->page('article/add-tag',['article'=>$article]);
    }

    public function addPost()
    {
        $article = $this->getArticleFromParam();
        $data['tag_title'] = $this->request->request->get('title');
        $data['tag_content'] = $this->request->request->get('content');
        $data['uid'] = current_uid();
        $data['article_id'] = $article->id;
        $pack = $this->article_tag_service->save($data);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-article_tag-search', ['zcode' => $article->zcode]);
        }
        $data['errors'] = $pack->getErrors();
        return $this->page('article/add-tag', [
            'article' => $article,
            'tag' => arr2dto($data, 'tag'),
            'errors' => $pack->getErrors()
        ]);
    }

    public function unlink()
    {
        $article = $this->getArticleFromParam();
        $tag_id = $this->getParam('tag_id');
        return $this->page('article/unlink', ['article' => $article, 'tag_id' => $tag_id]);
    }

    public function unlinkPost()
    {
        $article = $this->getArticleFromParam();
        $data['article_id'] = $this->request->request->get('article_id');
        $data['tag_id'] = $this->request->request->get('tag_id');
        $pack = $this->article_tag_service->delete($data);
        if (!$pack->isOk()) {
            return $this->page('article/unlink', ['errors' => $pack->getErrors()]);
        }
        return $this->gotoRoute('admin-ui-article_tag-search', ['zcode' => $article->zcode]);
    }

    protected function getArticleFromParam()
    {
        $article_zcode = $this->getParam('zcode');
        return $this->service('article')->getArticleByZcode($article_zcode);
    }

}