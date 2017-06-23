<?php
namespace Ipar\Ui;

class SearchCompanyController extends IparControllerBase {
    public function home() {
        return $this->page('index/home');
    }

    public function search()
    {
        return $this->page('search/company', [
            'query' => $this->request->query->get('query'),
            'type' => 'company'
        ]);
    }
}
