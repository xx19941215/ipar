<?php
namespace Ipar\Ui;

class SearchUserController extends IparControllerBase {
    public function home() {
        return $this->page('index/home');
    }

    public function search()
    {
        return $this->page('search/user', [
            'query' => $this->request->query->get('query'),
            'type' => 'user'
        ]);
    }
}
