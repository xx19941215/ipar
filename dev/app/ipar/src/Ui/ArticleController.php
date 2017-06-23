<?php
namespace Ipar\Ui;

class ArticleController extends IparControllerBase
{
    public function show()
    {
        $zcode = $this->getParam('zcode');

        $article = $this->service('article')->getArticleByZcode($zcode);

        /*
        $articles_i18n = $this->service('article')->schArticleSet([
            'type' => 'original_id',
            'query' => $article->original_id
        ]);
         */
        $query['article_id'] = $article->id;
        $tag_set = gap_service_manager()->make('article_tag')->search($query);
        return $this->page('article/show', [
            'article' => $article,
            'tag_set' => $tag_set,
            'is_tag' => true,
            //'articles_i18n' => $articles_i18n
        ]);
    }

    public function index()
    {
        return $this->page('article/index');
    }

}

?>
