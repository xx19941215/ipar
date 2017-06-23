<?php

namespace Admin\Ui;

class RqtCommentController extends AdminControllerBase
{
    public function index()
    {
        $rqt = $this->getRqtFromParam();
        $comment_set = $this->service('entity_comment')->search(['eid' => $rqt->eid,'status'=>null]);
        $page = $this->request->query->get('page');
        $comment_set = $comment_set->setCurrentPage($page);

        return $this->page('rqt/comment', [
            'rqt' => $rqt,
            'comment_set' => $comment_set,
        ]);
    }

    public function activate()
    {
        $rqt = $this->getRqtFromParam();
        $comment = $this->getCommentFromParam();
        return $this->page('rqt/activate-comment', ['comment' => $comment, 'rqt' => $rqt]);
    }

    public function activatePost()
    {
        $eid = $this->request->request->get('eid');
        $comment_id = $this->request->request->get('comment_id');
        $pack = $this->service('entity_comment')->activateComment($comment_id);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-rqt_comment-index', ['eid' => $eid]);
        }
    }

    public function deactivate()
    {
        $rqt = $this->getRqtFromParam();
        $comment = $this->getCommentFromParam();
        return $this->page('rqt/deactivate-comment', ['comment' => $comment, 'rqt' => $rqt]);
    }

    public function deactivatePost()
    {
        $eid = $this->request->request->get('eid');
        $comment_id = $this->request->request->get('comment_id');
        $pack = $this->service('entity_comment')->deactivateComment($comment_id);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-rqt_comment-index', ['eid' => $eid]);
        }
    }

    protected function getRqtFromParam()
    {
        $eid = $this->getParam('eid');
        return $this->service('rqt')->getRqtByEid($eid);
    }

    protected function getCommentFromParam()
    {
        $comment_id = $this->getParam('comment_id');
        return $this->service('entity_comment')->findCommentById($comment_id);
    }

}