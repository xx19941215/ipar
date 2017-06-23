<?php
namespace Ipar\Service;

class ActivityProductService extends IparServiceBase
{
    public function getProductSetByAid($activity_id = '')
    {
        return $this->repo('activity_product')->getProductSetByAid($activity_id);
    }

    public function getActivityProductSetByEid($aid = '')
    {
        return $this->repo('activity_product')->getActivityProductSetByEid($aid);
    }

    public function addActivityProductByEid($aid = '', $eid = '')
    {
        return $this->repo('activity_product')->addActivityProductByEid($aid, $eid);
    }

    public function deleteActivityProductByEid($aid = '', $eid = '')
    {
        return $this->repo('activity_product')->deleteActivityProductByEid($aid, $eid);
    }

    public function adviceProductToPage($aid = '', $eid = '')
    {
        return $this->repo('activity_product')->adviceProductToPage($aid, $eid);
    }

    public function cancelAdviceProductToPage($aid = '', $eid = '')
    {
        return $this->repo('activity_product')->cancelAdviceProductToPage($aid, $eid);
    }

    public function getActivityProductList()
    {
        return $this->repo('activity_product')->getActivityProductList();
    }

    public function activeProduct($aid, $eid)
    {
        return $this->repo('activity_product')->activeProduct($aid, $eid);
    }

    public function deActiveProduct($aid, $eid)
    {
        return $this->repo('activity_product')->deActiveProduct($aid, $eid);
    }

    public function getActiveProductEidByAid($aid)
    {
        return $this->repo('activity_product')->getActiveProductEidByAid($aid);
    }

    public function uploadImg($file = '')
    {
        if (!$file) {
            exit;
        }
        $bucket = 'ipar-activity';

        $access_id = config()->get("nos.$bucket.NOS_ACCESS_ID");
        $access_key = config()->get("nos.$bucket.NOS_ACCESS_KEY");
        $endpoint = config()->get("nos.$bucket.NOS_ENDPOINT");
        $nos = new \Ipar\Nos\Nos($access_id, $access_key, $endpoint, $bucket);
        $res = $nos->upload($file);
        $base = config()->get('site.nos_ipar_activity.host');
        $pic_url = $base . $res['dir'] . '/' . $res['name'] . '.' . $res['ext'];

        return $pic_url;
    }

    public function updateImgField($id = '', $type = '', $url = '', $db = 'activity_list')
    {
        return $this->repo('activity')->updateImgField($id, $type, $url, $db);
    }
}
