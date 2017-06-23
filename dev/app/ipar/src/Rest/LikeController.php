<?php
namespace Ipar\Rest;

class LikeController extends \Gap\Routing\Controller
{
    public function likeEntityPost()
    {
        $eid = $this->request->request->get('eid');

        return $this->service('entity_like')->like($eid);
    }

    public function unlikeEntityPost()
    {
        $eid = $this->request->request->get('eid');

        return $this->service('entity_like')->unlike($eid);
    }

    public function getLikeUser()
    {
        $eid = $this->request->request->get('eid');
        $page = $this->request->request->get('page');

        $page = $page ? $page : 1;

        $user_set = $this->service('entity_like')->schEntityLikeSet(['eid' => $eid]);
        $user_set->setCurrentPage($page);
        $users = [];
        $user_follow_service = $this->service('user_follow');
        foreach ($user_set->getitems() as $user_like) {
            $user = $user_like->getLikeUser();
            if (!isset($user->uid)) {
                continue;
            }
            $item['uid'] = $user->uid;
            $item['user_avt'] = $this->view('module/user-avt', ['user' => $user]);
            $item['user_nick'] = $user->nick;
            $item['is_following'] = $user_follow_service->isFollowing($user->uid) ? 'followed' : 'unfollow';
            $users[] = $item;
        }

        return $this->packitems([
            'page' => $page,
            'followed_users' => $users
        ]);
    }
}
