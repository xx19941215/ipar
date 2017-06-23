<?php
namespace Ipar\Ui;

class SearchRqtController extends IparControllerBase {
    public function home() {
        return $this->page('index/home');
    }

    public function search()
    {
        return $this->page('search/rqt', [
            'query' => $this->request->query->get('query'),
            'type' => 'rqt'
        ]);
    }
}
