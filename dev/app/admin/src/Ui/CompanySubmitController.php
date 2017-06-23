<?php

namespace Admin\Ui;

class CompanySubmitController extends AdminControllerBase
{
    public function index()
    {
        $count = array();
        $page = $this->request->query->get('page');
        $corporate_set = $this->service('company_submit')->getActivitySetAll();
        $corporate_set = $corporate_set->setCurrentPage($page);
        $count['all'] = $this->service('company_submit')->getCount();
        $count['handle'] = $this->service('company_submit')->getCount('handle');
        $count['unhandle'] = $this->service('company_submit')->getCount('unhandle');
        return $this->page('company_submit/index', [
            'corporate_set' => $corporate_set,
            'count' => $count,
            'page_count' => ceil($count['all']/10)
        ]);
    }

    public function detail()
    {
        $id = $this->getParam('id');
        $corporate_set = $this->service('company_submit')->getOneActivitySet($id);
        $mark_set = $this->service('company_submit')->getMarkSetById($id);
        return $this->page('company_submit/detail', [
            'corporate_set' => $corporate_set,
            'mark_set' => $mark_set
        ]);
    }

    public function addmark()
    {
        $url = route_url('admin-ui-activity-detail', ['id' => 1]);
        $admin = current_user()->nick;
        $mark_content = $this->request->query->get('mark_content');
        $uid = $this->request->query->get('uid');
        $isOk = $this->service('company_submit')->addOneMark($mark_content, $admin, $uid);

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }

    public function status()
    {
        $admin = current_user()->nick;
        $uid = $this->request->query->get('uid');
        $handle = $this->request->query->get('handle');
        $unhandle = $this->request->query->get('unhandle');
        $ignore = $this->request->query->get('ignore');
        $status = $handle . $unhandle . $ignore;
        $this->service('company_submit')->changeStatus($uid, $status, $admin);

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }

    public function search()
    {
        $search = $this->request->query->get('search');
        $status = $this->request->query->get('status');
        $search_set = $this->service('company_submit')->search($search, $status);

        return $this->page('company_submit/search', [
            'search_set' => $search_set
        ]);
    }

    public function delete()
    {
        $id = $this->getParam('id');
        $res = $this->service('company_submit')->delete($id);

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }
}
