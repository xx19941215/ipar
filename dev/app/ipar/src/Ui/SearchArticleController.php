<?php
namespace Ipar\Ui;

class SearchArticleController extends IparControllerBase {
    public function home() {
        return $this->page('index/home');
    }

    public function search()
    {
        return $this->page('search/article', [
            'query' => $this->request->query->get('query'),
            'type' => 'article'
        ]);
    }
}
