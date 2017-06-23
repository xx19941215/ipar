<?php

namespace Admin\Ui;

class IndustryTagController extends AdminControllerBase
{
    public function list()
    {
        $page = $this->request->query->get('page');
        $search = $this->request->query->get('query');
        $industry_tag_set = $this->service('industry_tag')->search(['search' => $search]);
        $industry_tag_set->setCurrentPage($page);

        return $this->page('industry_tag/index', [
            'industry_tag_set' => $industry_tag_set
        ]);
    }

    public function add()
    {
        return $this->page('industry_tag/add', [
            'industry_tag' => arr2dto([], 'industry_tag'),
        ]);
    }

    public function addPost()
    {
        $data = $this->getRequestData();
        $pack = $this->service('industry_tag')->save($data);

        if ($pack->isOK()) {
            return $this->gotoRoute('admin-ui-industry_tag-index');
        }

        return $this->page('industry_tag/add', [
            'industry_tag' => arr2dto($data, 'industry_tag'),
            'errors' => $pack->getErrors()
        ]);
    }

    public function delete()
    {
        $tag_id = $this->getParam('tag_id');
        $industry_tag = $this->service('industry_tag')->findOne(['tag_id' => $tag_id]);

        return $this->page('industry_tag/delete', [
            'industry_tag' => $industry_tag
        ]);

    }

    public function deletePost()
    {
        $tag_id = $this->request->request->get('tag_id');
        $pack = $this->service('industry_tag')->delete(['tag_id' => $tag_id]);
        $industry_tag = $this->service('industry_tag')->findOne(['tag_id' => $tag_id]);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-industry_tag-index');
        }

        return $this->page('industry_tag/delete', [
            'industry_tag' => $industry_tag,
            'errors' => $pack->getErrors()
        ]);
    }


    public function getRequestData()
    {
        $post = $this->request->request;

        return [
            'title' => $post->get('title'),
            'content' => $post->get('content'),
            'tag_id' => $post->get('tag_id'),
            'uid' => $post->get('uid'),
        ];
    }
}