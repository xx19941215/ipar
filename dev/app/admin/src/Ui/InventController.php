<?php

namespace Admin\Ui;

class InventController extends AdminControllerBase
{
    public function index()
    {
        $page = $this->request->query->get('page');
        $invents = $this->service('invent')->getRqts($page);
        $user = current_user();

        return $this->page('invent/index', [
            '$invents' => $invents,
            'user' => $user,
        ]);
    }
    public function show()
    {
        $invent = $this->getInventFromParam();
        return $this->page('invent/show', [
            'invent' => $invent
        ]);
    }

    public function edit()
    {
        $invent = $this->getInventFromParam();
        return $this->page('invent/edit', [
            'eid' => $invent->eid,
            'title' => $invent->title,
            'progress' => $invent->progress,
            'content' => $invent->content,
            'last_url' => $_SERVER['HTTP_REFERER'],
        ]);
    }

    public function editPost()
    {
        $eid = $this->request->request->get('eid');
        $content = $this->request->request->get('content');
        $title = $this->request->request->get('title');
        $progress = $this->request->request->get('progress');
        $last_url = $this->request->request->get('last_url');
        $pack = $this->service('invent')->updateInvent($eid, $title, $progress, $content);
        $rqt_eid = $this->service('rqt')->getRqtEidByDstId($eid);
        if ($pack->isOk()) {
            header('Location: '.$last_url);exit;
          //  return $this->gotoRoute('admin-rqt-invent',['eid' => $rqt_eid]);
        } else {
            return $this->page('invent/edit', [
                'eid' => $eid,
                'title' => $title,
                'content' => $content,
                'progress' => $progress,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    public function delete()
    {
        return $this->page('invent/delete', [
            'invent' => $this->getInventFromParam(),
            'last_url' => $_SERVER['HTTP_REFERER'],
        ]);
    }

    public function deletePost()
    {
        $param_eid = $this->getParam('eid');
        $post_eid = $this->request->request->get('eid');
        $post_e_type = $this->request->request->get('type');
        $post_rqt_eid = $this->request->request->get('rqt_eid');
        $last_url = $this->request->request->get('last_url');
        if ($param_eid != $post_eid) {
            die('error request');
        }
        $rqt_eid = $this->service('rqt')->getRqtEidByDstId($post_eid);
        $pack = $this->service('invent')->deleteInvent($post_eid);
        if ($pack->isOk()) {
            header('Location: '.$last_url);exit;
          //  return $this->gotoRoute('admin-rqt-'.$post_e_type, ['eid' => $rqt_eid]);
        } else {
            return $this->page('invent/delete', [
                'invent' => $this->getInventFromParam(),
                'errors' => $pack->getErrors()
            ]);
        }
    }

    protected function getInventFromParam()
    {
        $eid = $this->getParam('eid');
        return $this->service('invent')->getEntityByEid($eid);
    }
}
