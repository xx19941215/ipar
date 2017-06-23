<?php

namespace Admin\Ui;

class BrandTagController extends AdminControllerBase
{
    public function list()
    {
        $page = $this->request->query->get('page');
        $search = $this->request->query->get('query');
        $brand_tag_set = $this->service('brand_tag')->search(['search' => $search]);
        $brand_tag_set->setCurrentPage($page);

        return $this->page('brand_tag/index', [
            'brand_tag_set' => $brand_tag_set
        ]);
    }
}