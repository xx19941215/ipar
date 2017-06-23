<?php

namespace Admin\Ui;

class ActivityController extends AdminControllerBase
{
    public function addActivity()
    {
        return $this->page(
            'activity/add_activity'
        );
    }

    public function editActivity()
    {
        $activity_id = $this->request->query->get('id');
        $set = $this->service('activity')->getSingleActivitySet($activity_id);
        return $this->page(
            'activity/edit_activity',
            ['set' => $set->getItems()]
        );
    }

    public function activityList()
    {
        $page = $this->request->query->get('page');
        $set = $this->service('activity')->getActivitySet();
        $set = $set->setCountPerPage(10);
        $set = $set->setCurrentPage($page);
        $pageCount = $set->getPageCount();
        $set = $set->getItems();
        return $this->page(
            'activity/list_activity',
            [
                'set' => $set,
                'pageCount' => $pageCount
            ]
        );
    }

    public function addIndexBanner()
    {
        $activity_id = $this->request->query->get('id');
        $pic_set = $this->service('activity')->getSingleActivitySet($activity_id);
        return $this->page(
            'activity/add_index_banner',
            [
                'activity_id' => $activity_id,
                'pic_set' => $pic_set->getItems()
            ]
        );
    }

    public function addPost()
    {
        $item['title'] = $this->request->request->get('title');
        $item['description'] = $this->request->request->get('description');
        $item['createtime'] = $this->request->request->get('createtime');
        $item['endtime'] = $this->request->request->get('endtime');
        $item['rule'] = $this->request->request->get('rule');
        $res = $this->service('activity')->addSingleData($item);

        $list_activity_url = route_url('admin-activity-list');
        echo "<script>location.href='{$list_activity_url}';</script>";
    }

    public function addImg()
    {
        if (isset($_FILES['img_index'])) {
            $type = 'img_index';
        } elseif (isset($_FILES['img_banner'])) {
            $type = 'img_banner';
        }
        $file = $this->request->files->get($type);
        $id = $this->request->request->get('id');
        $pic_url = $this->service('activity')->uploadImg($file);
        $res = $this->service('activity')->updateImgField($id, $type, json_encode('http://' . $pic_url));
        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }

    public function editPost()
    {
        $item['id'] = $this->request->request->get('id');
        $item['title'] = $this->request->request->get('title');
        $item['description'] = $this->request->request->get('description');
        $item['rule'] = $this->request->request->get('rule');
        $item['createtime'] = $this->request->request->get('createtime');
        $item['endtime'] = $this->request->request->get('endtime');
        $this->service('activity')->updateSingleData($item);

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }

    public function adviceToIndexPage()
    {
        $id = $this->request->get('id');
        $this->service('activity')->adviceToIndexPage($id);

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }

    public function cancelAllAdviceToIndexPage()
    {
        $this->service('activity')->cancelAllAdviceToIndexPage();

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }

    public function deleteActivity()
    {
        $aid = $this->request->get('id');
        $this->service('activity')->deleteActivity($aid);

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }
}
