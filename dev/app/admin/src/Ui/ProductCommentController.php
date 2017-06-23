<?php

namespace Admin\Ui;

class ProductCommentController extends AdminControllerBase
{
    public function index()
    {
        $product = $this->getProductFromParam();
        $comment_set = $this->service('entity_comment')->search(['eid' => $product->eid,'status'=>null]);
        $page = $this->request->query->get('page');
        $comment_set = $comment_set->setCurrentPage($page);

        return $this->page('product/comment', [
            'product' => $product,
            'comment_set' => $comment_set,
        ]);
    }

    public function activate()
    {
        $product = $this->getProductFromParam();
        $comment = $this->getCommentFromParam();
        return $this->page('product/activate-comment', ['comment' => $comment, 'product' => $product]);
    }

    public function activatePost()
    {
        $eid = $this->request->request->get('eid');
        $comment_id = $this->request->request->get('comment_id');
        $pack = $this->service('entity_comment')->activateComment($comment_id);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-product_comment-index', ['eid' => $eid]);
        }
    }

    public function deactivate()
    {
        $product = $this->getProductFromParam();
        $comment = $this->getCommentFromParam();
        return $this->page('product/deactivate-comment', ['comment' => $comment, 'product' => $product]);
    }

    public function deactivatePost()
    {
        $eid = $this->request->request->get('eid');
        $comment_id = $this->request->request->get('comment_id');
        $pack = $this->service('entity_comment')->deactivateComment($comment_id);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-product_comment-index', ['eid' => $eid]);
        }
    }

    protected function getProductFromParam()
    {
        $eid = $this->getParam('eid');
        return $this->service('product')->getProductByEid($eid);
    }

    protected function getCommentFromParam()
    {
        $comment_id = $this->getParam('comment_id');
        return $this->service('entity_comment')->findCommentById($comment_id);
    }

}