<?php

namespace Admin\Ui;

class IdeaController extends AdminControllerBase
{
    public function index()
    {
        $query = $this->request->query->get('query');
        $idea_set = $this->service('idea')->schIdeaSet(['query' => $query, 'status' => null]);

        $idea_set->setCurrentPage((int)$this->request->get('page'));

        return $this->page('idea/index', [
            'query' => $query,
            'idea_set' => $idea_set,
        ]);
    }

    public function show()
    {
        $idea = $this->getIdeaFromParam();
        $story_set = gap_service_manager()->make('story')->search(['eid' => $idea->eid]);

        return $this->page('idea/show', [
            'idea' => $idea,
            'story_set' => $story_set,
        ]);
    }

    public function content()
    {
        return $this->page('idea/content', [
            'idea' => $this->getIdeaFromParam(),
        ]);
    }

    public function edit()
    {
        $idea = $this->getIdeaFromParam();
        $this->prepareTargetUrl();

        return $this->page('idea/edit', [
            'eid' => $idea->eid,
            'content' => $idea->content,
        ]);
    }

    public function editPost()
    {
        $eid = $this->request->request->get('eid');
        $content = $this->request->request->get('content');
        $pack = $this->service('idea')->updateIdea($eid, $content);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-idea-show', ['eid' => $eid]);
        } else {
            return $this->page('idea/edit', [
                'eid' => $eid,
                'content' => $content,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    public function delete()
    {
        return $this->page('idea/delete', [
            'idea' => $this->getIdeaFromParam(),
            'last_url' => prop($_SERVER, 'HTTP_REFERER'),
        ]);
    }

    public function deletePost()
    {
        $param_eid = $this->getParam('eid');
        $post_eid = $this->request->request->get('eid');
        /*
        $post_e_type = $this->request->request->get('type');
        $post_rqt_eid = $this->request->request->get('rqt_eid');
        $last_url = $this->request->request->get('last_url');
         */
        if ($param_eid != $post_eid) {
            die('error request');
        }
        $pack = $this->service('idea')->deleteIdea($post_eid);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-idea');
        } else {
            return $this->page('idea/delete', [
                'idea' => $this->getIdeaFromParam(),
                'errors' => $pack->getErrors()
            ]);
        }
    }

    protected function getIdeaFromParam()
    {
        $eid = $this->getParam('eid');
        return $this->service('idea')->getEntityByEid($eid);
    }
}
