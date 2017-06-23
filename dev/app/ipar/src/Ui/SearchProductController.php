<?php
namespace Ipar\Ui;

class SearchProductController extends IparControllerBase
{
    public function home()
    {
        return $this->page('index/home');
    }

    public function search()
    {
        return $this->page('search/product', [
            'query' => $this->request->query->get('query'),
            'type' => 'product'
        ]);
    }
}
