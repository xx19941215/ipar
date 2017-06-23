<?php

namespace Admin\Ui;

class ActivityProductController extends AdminControllerBase
{
    public function activeProduct()
    {
        $aid = $this->request->get('aid');
        $eid = $this->request->get('eid');
        $this->service('activity_product')->activeProduct($aid, $eid);

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }

    public function deActiveProduct()
    {
        $aid = $this->request->get('aid');
        $eid = $this->request->get('eid');
        $this->service('activity_product')->deActiveProduct($aid, $eid);

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }

    public function listProduct()
    {
        $item = [];
        $aid = $this->request->get('id');
        $page = $this->request->query->get('page');
        $product_set = $this->service('activity_product')->getProductSetByAid($aid);
        $product_set = $product_set->setCountPerPage(10);
        $product_set = $product_set->setCurrentPage($page);
        $pageCount = $product_set->getPageCount();
        $product_set = $product_set->getItems();

        $active_id_list = $this->service('activity_product')->getActiveProductEidByAid($aid);

        foreach ($product_set as $v) {
            $item[] = $this->service('product')->getProductByEid($v->product_id);
        }
        return $this->page(
            'activity/list_product',
            [
                'product_set' => $item,
                'aid' => $aid,
                'pageCount' => $pageCount,
                'active_id_list' => $active_id_list
            ]
        );
    }

    public function searchProduct()
    {
        $search = $this->request->request->get('search');
        $aid = $this->request->get('aid');
        $product_set = service('entity_search')
            ->suggest([
                'type_key' => 'product',
                'query' => $search
            ])
            ->setCountPerPage(10);
        return $this->page(
            'activity/search_product',
            [
                'product_set' => $product_set->getItems(),
                'aid' => $aid
            ]
        );
    }

    public function changeProductImg()
    {
        // $host = config()->get('site.static.host');
        $id = $this->request->get('id');
        $file = $this->request->files->get('file');
        $pic_url = $this->service('activity_product')->uploadImg($file);
        $res = $this->service('activity_product')
            ->updateImgField($id, 'changed_img', 'http://' . $pic_url, 'activity_product');

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }

    public function cancelAdviceProduct()
    {
        $eid = $this->request->query->get('eid');
        $aid = $this->request->query->get('aid');
        $set = $this->service('activity_product')->cancelAdviceProductToPage($aid, $eid);

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }

    public function adviceProduct()
    {
        $trans_advice = trans('you-have-adviced-3');
        $eid = $this->request->query->get('eid');
        $aid = $this->request->query->get('aid');
        $set = $this->service('activity_product')->adviceProductToPage($aid, $eid);
        if (!$set) {
            echo "<script>alert('$trans_advice');</script>";
        }

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }

    public function editProduct()
    {
        $aid = $this->request->query->get('id');
        $set = $this->service('activity_product')->getActivityProductSetByEid($aid)->getItems();
        return $this->page(
            'activity/edit_product',
            [
                'set' => $set
            ]
        );
    }

    public function deleteProduct()
    {
        $eid = $this->request->query->get('eid');
        $aid = $this->request->query->get('aid');
        $this->service('activity_product')->deleteActivityProductByEid($aid, $eid);

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }

    public function addProduct()
    {
        $trans_have_add = trans('product-have-add');
        $eid = $this->request->query->get('eid');
        $aid = $this->request->query->get('aid');
        $res = $this->service('activity_product')->addActivityProductByEid($aid, $eid);
        if (!$res) {
            echo "<script>alert('$trans_have_add');</script>";
        }

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }

    public function selectProduct()
    {
        $item = [];
        $page = $this->request->query->get('page');
        $activity_id = $this->request->query->get('id');
        $set = $this->service('product')->schProductSet();
        $set = $set->setCountPerPage(10)->setCurrentPage($page);
        $pageCount = $set->getPageCount();
        $set = $set->getItems();
        $host = config()->get('site.static.host');
        foreach ($set as $s) {
            $arr_img = $s->getImgs();
            $item['img_url'] = 'http://' . $arr_img[0]['dir'] . '/' .
                        $arr_img[0]['name'] . '.' . $arr_img[0]['ext'];
            $item['title'] = $s->title;
            $item['eid'] = $s->eid;
            $arr[] = $item;
        }
        return $this->page(
            'activity/select_product',
            [
                'set' => $arr,
                'aid' => $activity_id,
                'pageCount' => $pageCount
            ]
        );
    }
}
