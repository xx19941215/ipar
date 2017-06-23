<?php
namespace Ipar\Service;

class ActivityService extends IparServiceBase
{
    public function getActivitySet()
    {
        return $this->repo('activity')->getActivitySet();
    }

    public function getSingleActivitySet($activity_id = '')
    {
        return $this->repo('activity')->getSingleActivitySet($activity_id);
    }

    public function addSingleData($item)
    {
        return $this->repo('activity')->addSingleData($item);
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

    public function updateSingleData($item = [])
    {
        return $this->repo('activity')->updateSingleData($item);
    }

    public function adviceToIndexPage($id = '')
    {
        return $this->repo('activity')->adviceToIndexPage($id);
    }

    public function getActivitySetPreview()
    {
        return $this->repo('activity')->getActivitySetPreview();
    }

    public function getActivityDate()
    {
        return $this->repo('activity')->getActivityDate();
    }

    public function getActivityIndexImg()
    {
        return $this->repo('activity')->getActivityIndexImg();
    }

    public function cancelAllAdviceToIndexPage()
    {
        return $this->repo('activity')->cancelAllAdviceToIndexPage();
    }

    public function deleteActivity($id)
    {
        return $this->repo('activity')->deleteActivity($id);
    }

    public function getPopImproving()
    {
        return $this->repo('activity')->getPopImproving();
    }

    public function getProductTitle($hotList)
    {
        return $this->repo('activity')->getProductTitle($hotList);
    }

    public function getIsLike()
    {
        return $this->repo('activity')->getIsLike();
    }

    public function getLikeCount()
    {
        return $this->repo('activity')->getLikeCount();
    }
}
