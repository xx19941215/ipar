<?php

namespace Admin\Ui;

class ArticleController extends AdminControllerBase
{
    public function index()
    {
        $article_set = $this->service('article')->getArticleSetAll();
        $article_set->setCurrentPage((int)$this->request->get('page'));

        return $this->page('article/index', [
            'article_set' => $article_set,
        ]);
    }

    public function show()
    {
        $article_zcode = $this->getParam('zcode');
        $article = $this->service('article')->getArticleByZcode($article_zcode);

        $articles_i18n = $this->service('article')->schArticleSet([
            'type' => 'original_id',
            'query' => $article->original_id
        ]);

        $this->prepareTargetUrl();
        $data['article_id'] = $article->id;
        $tag_set = gap_service_manager()->make('article_tag')->search($data);
        return $this->page('article/show', [
            'article' => $article,
            'id' => $article->id,
            'created' => $article->created,
            'changed' => $article->changed,
            'status' => $article->status,
            'locale_id' => $article->locale_id,
            'zcode' => $article->zcode,
            'title' => $article->title,
            'content' => $article->content,
            'articles_i18n' => $articles_i18n,
            'tag_set' => $tag_set,
        ]);
    }

    public function add()
    {
        $this->prepareTargetUrl();
        return $this->page('article/add');
    }

    public function addLocale()
    {
        $original_id = $this->getParam('original_id');
        return $this->page('article/add', [
            'original_id' => $original_id
        ]);

    }

    public function addPost()
    {
        $uid = $this->request->request->get('uid');
        $title = $this->request->request->get('title');
        $content = $this->request->request->get('content');
        $locale_id = $this->request->get('locale_id');
        $tag_ids = []; // TODO: add tag to article
        $original_id = $this->request->request->get('original_id');

        $pack = $this->service('article')->createArticle($title, $content, $locale_id, $original_id);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-article-index');
            /*
            return $this->gotoRoute('admin-article-show', [
                'id' => $pack->getItem('id')
            ]);
             */
        }

        return $this->page('article/add', [
            'title' => $title,
            'content' => $content,
            'url' => $url,
            'errors' => $pack->getErrors(),
        ]);
    }

    public function edit()
    {
        $id = $this->request->request->get('id');
        $zcode = $this->request->request->get('zcode');

        $uid = $this->request->request->get('uid');
        $title = $this->request->request->get('title');
        $content = $this->request->request->get('content');
        $locale_id = $this->request->get('locale_id');
        $original_id = ''; // TODO: add original_id to article
        $tag_ids = []; // TODO: add tag to article

        $opts = ['uid' => $uid, 'title' => $title, 'content' => $content, 'locale_id' => $locale_id];


        $pack = $this->service('article')->editArticle($id, $opts);

        if ($pack->isOk()) {

            return $this->gotoRoute('admin-article-show', [
                'zcode' => $zcode
            ]);
        }

        return $this->page('article/add', [
            'title' => $title,
            'content' => $content,
            'locale_id' => $locale_id,
            'url' => $url,
            'errors' => $pack->getErrors(),
        ]);

    }

    public function deactivate()
    {
        $zcode = $this->getParam('zcode');
        $article = $this->service('article')->getArticleByZcode($zcode);
        return $this->page('article/deactivate', [
            'article' => $article
        ]);
    }

    public function deactivatePost()
    {
        $zcode = $this->getParam('zcode');
        $article = $this->service('article')->getArticleByZcode($zcode);
        $id = $article->id;
        $pack = $this->service('article')->deactivate($id);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-article-show', ['zcode' => $zcode]);
        } else {
            return $this->page('article/deactivate', [
                'article' => $article
            ]);
        }
    }

    public function activate()
    {
        $zcode = $this->getParam('zcode');
        $article = $this->service('article')->getArticleByZcode($zcode);
        return $this->page('article/activate', [
            'article' => $article
        ]);
    }

    public function activatePost()
    {
        $zcode = $this->getParam('zcode');
        $article = $this->service('article')->getArticleByZcode($zcode);
        $id = $article->id;
        $pack = $this->service('article')->activate($id);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-article-show', ['zcode' => $zcode]);
        }

        return $this->page('article/activate', [
            'article' => $article
        ]);
    }

    public function delete()
    {
        $zcode = $this->getParam('zcode');
        $article = $this->service('article')->getArticleByZcode($zcode);
        return $this->page('article/delete', [
            'article' => $article
        ]);
    }

    public function deletePost()
    {
        $zcode = $this->getParam('zcode');


        $article = $this->service('article')->getArticleByZcode($zcode);

        $id = $article->id;
        $pack = $this->service('article')->deleteArticle($id);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-article-index');
        }

        return $this->page('article/index', [
            'article' => $article
        ]);
    }

    public function deleteLocale()
    {
        $zcode = $this->getParam('zcode');
        $article = $this->service('article')->getArticleByZcode($zcode);
        return $this->page('article/delete-locale', [
            'article' => $article
        ]);
    }

    public function deleteLocalePost()
    {
        $zcode = $this->getParam('zcode');
        $article = $this->service('article')->getArticleByZcode($zcode);
        $id = $article->id;
        $pack = $this->service('article')->deleteLocale($id);

        $original_article_zcode = $this->service('article')->getArticleById($article->original_id)->zcode;

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-article-show', ['zcode' => $original_article_zcode]);
        }

        return $this->page('article/activate', [
            'article' => $article
        ]);
    }

}
