<?php

namespace Admin\Ui;

class RqtController extends AdminControllerBase
{
    public function index()
    {
        $query = $this->request->query->get('query');
        $rqt_set = $this->service('rqt')->schRqtSet(['query' => $query, 'status' => null, 'sort' => 1,'type_key' => 'rqt']);
        $rqt_set->setCurrentPage((int) $this->request->get('page'));

        return $this->page('rqt/index', [
            'rqt_set' => $rqt_set
        ]);
    }

    public function search()
    {
        return $this->page('rqt/search');
    }

    public function add()
    {
        $this->prepareTargetUrl();
        return $this->page('rqt/add');
    }

    public function addPost()
    {
        $title = $this->request->request->get('title');
        $content = $this->request->request->get('content');
        $pack = $this->service('rqt')->createRqt($title, $content);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-rqt-show', [
                'eid' => $pack->getItem('eid')
            ]);
            //return $this->gotoRoute('admin-rqt');
        } else {
            return $this->page('rqt/add', [
                'title' => $title,
                'content' => $content,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    public function show()
    {
        $rqt = $this->getRqtFromParam();
        $story_set = gap_service_manager()->make('story')->search(['eid' => $rqt->eid]);
//        $tag_set = $this->getTagSet();
        return $this->page('rqt/show', [
            'rqt' => $rqt,
            'story_set' => $story_set,
//            'tag_set' => $tag_set
        ]);
    }

    public function content()
    {
        return $this->page('rqt/content', [
            'rqt' => $this->getRqtFromParam(),
        ]);
    }

    public function edit()
    {
        $rqt = $this->getRqtFromParam();
        $this->prepareTargetUrl();

        return $this->page('rqt/edit', [
            'rqt' => $rqt,
            'eid' => $rqt->eid,
            'title' => $rqt->title,
            'content' => $rqt->content,
        ]);
    }

    public function editPost()
    {
        $eid = $this->request->request->get('eid');
        $title = $this->request->request->get('title');
        $content = $this->request->request->get('content');
        $pack = $this->service('rqt')->updateRqt($eid, $title, $content);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-rqt-show', ['eid' => $eid]);
        } else {
            return $this->page('rqt/edit', [
                'eid' => $eid,
                'title' => $title,
                'content' => $content,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    public function delete()
    {
        if ($referer = $this->request->headers->get('referer')) {
            $this->setTargetUrl($referer);
        }
        return $this->page('rqt/delete', [
            'rqt' => $this->getRqtFromParam(),
        ]);
    }

    public function deletePost()
    {
        $param_eid = $this->getParam('eid');
        $post_eid = $this->request->request->get('eid');

        if ($param_eid != $post_eid) {
            die('error request');
        }
        $pack = $this->service('rqt')->deleteRqt($post_eid);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-rqt');
            //return $this->gotoTargetUrl('', route_url('admin-rqt'));
        } else {
            return $this->page('product/delete', [
                'rqt' => $this->getRqtFromParam(),
                'errors' => $pack->getErrors()
            ]);
        }
    }

    /*
     * solution
     */

    public function solution()
    {
        $rqt = $this->getRqtFromParam();
        $query = $this->request->query->get('query', '');
        $solution_set = $this->service('rqt')->schSolutionSet($rqt->eid, ['query' => $query, 'status' => null]);

        return $this->page('rqt/solution', [
            'rqt' => $rqt,
            'query' => $query,
            'solution_set' => $solution_set,
        ]);
    }

    //product
    public function product()
    {
        $rqt = $this->getRqtFromParam();
        $query = $this->request->query->get('query', '');
        $product_set = $this->service('rqt')->schSproductSet($rqt->eid, ['query' => $query, 'status' => null]);

        return $this->page('rqt/product', [
            'rqt' => $rqt,
            'query' => $query,
            'product_set' => $product_set,
        ]);
    }

    public function addProduct()
    {
        $query = $this->request->query->get('query');
        $product_set = $this->service('product')->schProductSet(['query' => $query, 'status' => null]);
        $this->prepareTargetUrl();
        return $this->page('rqt/add-product', [
            'product_set' => $product_set,
            'rqt' => $this->getRqtFromParam(),
            'query' => $query
        ]);
    }

    public function addProductPost()
    {
        $rqt = $this->getRqtFromParam();
        $dst_eid = (int) $this->request->request->get('dst_eid');

        if ($dst_eid) {
            $pack = $this->service('rqt')->createSolution($rqt->eid, 'product', $dst_eid);
        } else {
            $title = $this->request->request->get('title');
            $content = $this->request->request->get('content');
            $url = $this->request->get('url');
            $pack = $this->service('rqt')->createSproduct($rqt->eid, $title, $content, $url);
        }

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-product-show', ['eid' => $pack->getItem('product_eid')]);
        } else {
            return $this->page('rqt/add-product', [
                'rqt' => $rqt,
                'title' => $title,
                'content' => $content,
                'url' => $url,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    // idea
    public function idea()
    {
        $rqt = $this->getRqtFromParam();
        $query = $this->request->query->get('query', '');
        $idea_set = $this->service('rqt')->schSideaSet($rqt->eid, ['query' => $query, 'status' => null]);

        return $this->page('rqt/idea', [
            'rqt' => $rqt,
            'query' => $query,
            'idea_set' => $idea_set,
        ]);
    }

    public function addIdea()
    {
        return $this->page('rqt/add-idea', [
            'rqt' => $this->getRqtFromParam()
        ]);
    }

    public function addIdeaPost()
    {
        if (!$rqt = $this->getRqtFromParam()) {
            _debug('error request');
        }
        $content = $this->request->request->get('content');
        $pack = $this->service('rqt')->createSidea($rqt->eid, $content);

        if ($pack->isOk()) {
            return $this->gotoRoute('admin-idea-show', ['eid' => $pack->getItem('idea_eid')]);
            exit();
        } else {
            return $this->page('rqt/add-idea', [
                'rqt' => $rqt,
                'content' => $content,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    // invent
    public function invent()
    {
        _debug('todo');
        $rqt = $this->getRqtFromParam();
        $page = $this->request->query->get('page');
        $invents = $this->service('invent')->getInventsByRqtEid($rqt->eid, $page, null);
        $page_count =  $this->service('invent')->getInventPageCountByRqtEid($rqt->eid, null);
        $user = $this->getCurrentUser();

        return $this->page('rqt/invent', [
            'rqt' => $rqt,
            'invents' => $invents,
            'user' => $user,
            'page_count' => $page_count,
            'page' => $page,
        ]);
    }

    public function addInvent()
    {
        _debug('todo');
        return $this->page('rqt/add-invent', [
            'rqt' => $this->getRqtFromParam()
        ]);
    }

    public function addInventPost()
    {
        _debug('todo');
        $rqt = $this->getRqtFromParam();
        $content = $this->request->request->get('content');
        $title = $this->request->request->get('title');
        $progress = $this->request->request->get('progress');
        $pack = $this->service('rqt')->createInvent($rqt->eid,$title, $content,$progress);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-rqt-invent', ['eid' => $rqt->eid]);
            exit();
        } else {
            return $this->page('rqt/add-invent', [
                'rqt' => $rqt,
                'title' => $title,
                'content' => $content,
                'progress' => $progress,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    public function getTagSet()
    {
        $rqt = $this->getRqtFromParam();
        $data['eid'] = $rqt->eid;
        $data['entity_type_id']= $rqt->type_id;
        $tag_set = gap_service_manager()->make('entity_tag')->search($data);
        return $tag_set;
    }

    /*
     * Protected
     */

    protected function getRqtFromParam()
    {
        $eid = $this->getParam('eid');
        return $this->service('rqt')->getRqtByEid($eid);
        //return $this->service('rqt')->getEntityByEid($eid);
    }

}
