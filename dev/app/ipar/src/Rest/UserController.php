<?php
/**
 * PHP Version 5.6.
 *
 * @category Rest
 *
 * @author Zjh <zhanjh@126.com>
 * @license http:://www.tecposter.cn/bsd-licence BSD Licence
 *
 * @link http:://www.tecposter.cn/
 **/

namespace Ipar\Rest;

use Gap\Routing\Controller as RoutingController;

class UserController extends RoutingController
{
    public function search()
    {

        $user_set = $this->service('user_search')->schUserSet([
            'query' => $this->request->query->get('query')
        ]);

        $user_set->setCountPerPage(4)->setCurrentPage($this->request->query->get('page'));

        $arr = [];

        foreach ($user_set->getItems() as $user) {
            $item = [];
            $item['uid'] = $user->uid;
            $item['nick'] = $user->nick;
            $item['zcode'] = $user->zcode;
            $item['url'] = route_url("ipar-article-show", ['zcode' => $user->zcode]);
            $item['user'] = $user;
            $item['user_url'] = $user->getUrl();
            $item['avt_url'] = img_src((array)json_decode($user->avt), 'small');

            $arr[] = $item;
        }


        return $this->packItem('user', $arr);
    }

    public function uploadAvtPost()
    {
        if (!$img_file = $this->request->files->get('img')) {
            return $this->badRequest();
        }

        $bucket = 'ipar-avt';

        $access_id = config()->get("nos.$bucket.NOS_ACCESS_ID");
        $access_key = config()->get("nos.$bucket.NOS_ACCESS_KEY");
        $endpoint = config()->get("nos.$bucket.NOS_ENDPOINT");
        $nos = new \Ipar\Nos\Nos($access_id, $access_key, $endpoint, $bucket);
        $res = $nos->upload($img_file);

        $avt = [
            'site' => config()->get('avt.nos_ipar_avt'),
            'dir' => $res['dir'],
            'name' => $res['name'],
            'ext' => $res['ext']
        ];

        $user_service = $this->service('user');
        if ($user_service->updateAvt($avt)->isOk()) {
            return $this->packItem('avt_url', img_src($avt, 'medium'));
        }
        return '';
    }
}
