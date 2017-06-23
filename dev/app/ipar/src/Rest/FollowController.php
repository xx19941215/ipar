<?php
namespace Ipar\Rest;

use Gap\Routing\Controller as RoutingController;

class FollowController extends RoutingController
{
    public function followUserPost()
    {
        $uid = $this->request->request->get('uid');
        if (!$uid) {
            return $this->packError('uid', 'uid-empty');
        }
        $user_follow_service = $this->service('user_follow');
        $followed = $user_follow_service->follow($uid);
        if (!$followed) {
            return $this->packError('follow', 'failed');
        }
        
        $is_following = $user_follow_service->isFollowing($uid);

        $followed_count = $user_follow_service->countFollowed($uid);

        $following_count = $user_follow_service->countFollowing($uid);


        return $this->packitems([
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'following_count' => $following_count
        ]);
    }

    public function unfollowUserPost()
    {
        $uid = $this->request->request->get('uid');
        if (!$uid) {
            return $this->packError('uid', 'uid-empty');
        }
        $user_follow_service = $this->service('user_follow');
        $unfollow = $user_follow_service->unfollow($uid);
        if (!$unfollow) {
            return $this->packError('unfollow', 'failed');
        }
 
        $is_following = $user_follow_service->isFollowing($uid);

        $followed_count = $user_follow_service->countFollowed($uid);

        $following_count = $user_follow_service->countFollowing($uid);

        return $this->packitems([
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'following_count' => $following_count
        ]);
    }

    public function followEntityPost()
    {
        $eid = $this->request->request->get('eid');
        if (!$eid) {
            return $this->packError('eid', 'eid-empty');
        }
        $entity_follow_service = $this->service('entity_follow');
        $followed = $entity_follow_service->follow($eid);
        if (!$followed) {
            return $this->packError('follow', 'failed');
        }
        $is_following = $entity_follow_service->isFollowing($eid);

        $followed_count = $entity_follow_service->countFollowed($eid);

        $following_count = $entity_follow_service->countFollowing($eid);

        $followed_set = $entity_follow_service->fetchFollowedSet($eid);
        $followed_users = [];
        foreach ($followed_set->getitems() as $user_follow) {
            $user = $user_follow->getFollowedUser();
            $followed_users[] = $this->view('module/user-avt', ['user' => $user]);
        }
        return $this->packitems([
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'following_count' => $following_count,
            'followed_users' => $followed_users
        ]);
        
    }

    public function unfollowEntityPost()
    {
        $eid = $this->request->request->get('eid');
        if (!$eid) {
            return $this->packError('eid', 'eid-empty');
        }
        $entity_follow_service = $this->service('entity_follow');
        $response = $entity_follow_service->unfollow($eid);
        if (!$response) {
            return $this->packError('msg', $response);
        }
        $is_following = $entity_follow_service->isFollowing($eid);

        $followed_count = $entity_follow_service->countFollowed($eid);

        $following_count = $entity_follow_service->countFollowing($eid);

        $followed_set = $entity_follow_service->fetchFollowedSet($eid);
        $followed_users = [];
        foreach ($followed_set->getitems() as $user_follow) {
            $user = $user_follow->getFollowedUser();
            $followed_users[] = $this->view('module/user-avt', ['user' => $user]);
        }
        return $this->packitems([
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'following_count' => $following_count,
            'followed_users' => $followed_users
        ]);
    }

    public function userCommonUsers()
    {
        $dst_uid = $this->request->request->get('dst_uid');
        if (!$dst_uid) {
            return $this->packError('dst_uid', 'dst_uid-empty');
        }
        $user_follow_service = service('user_follow');

        $common_user_set = $user_follow_service->fetchCommonUserSet($dst_uid);

        $common_count = $common_user_set->getItemCount();
        $common_users = [];
        foreach ($common_user_set->getitems() as $user_follow) {
            $user = $user_follow->getFollowedUser();
            $common_users[] = $this->view('module/user-avt', ['user' => $user]);
        }

        return $this->packitems([
            'common_count' => $common_count,
            'common_users' => $common_users
        ]);
    }

    public function userFollowedUsers()
    {
        $dst_uid = $this->request->request->get('uid');
        $page = $this->request->request->get('page');

        $page = $page ? $page : 1;
        if (!$dst_uid) {
            return $this->packError('uid', 'uid-empty');
        }

        $user_follow_service = $this->service('user_follow');

        $followed_set = $user_follow_service->fetchFollowedSet($dst_uid)->setCountPerPage(5)->setCurrentPage($page);
        $followed_users = [];
        foreach ($followed_set->getitems() as $user_follow) {
            $user = $user_follow->getFollowedUser();
            $item['uid'] = $user->uid;
            $item['user_avt'] = $this->view('module/user-avt', ['user' => $user]);
            $item['user_nick'] = $user->nick;
            $item['is_following'] = $user_follow_service->isFollowing($user->uid) ? 'followed' : 'unfollow';
            $followed_users[] = $item;
        }
        return $this->packitems([
            'page' => $page,
            'followed_users' => $followed_users
        ]);
    }

    public function userFollowingUsers()
    {
        $uid = $this->request->request->get('uid');
        $page = $this->request->request->get('page');

        $page = $page ? $page : 1;
        if (!$uid) {
            return $this->packError('uid', 'uid-empty');
        }

        $user_follow_service = $this->service('user_follow');

        $followed_set = $user_follow_service->fetchFollowingSet($uid)->setCountPerPage(5)->setCurrentPage($page);
        $followed_users = [];
        foreach ($followed_set->getitems() as $user_follow) {
            $user = $user_follow->getFollowingUser();
            $item['uid'] = $user->uid;
            $item['user_avt'] = $this->view('module/user-avt', ['user' => $user]);
            $item['user_nick'] = $user->nick;
            $item['is_following'] = $user_follow_service->isFollowing($user->uid) ? 'followed' : 'unfollow';
            $followed_users[] = $item;
        }
        return $this->packitems([
            'page' => $page,
            'followed_users' => $followed_users
        ]);
    }

    public function entityFollowedUsers()
    {
        $eid = $this->request->request->get('eid');
        $page = $this->request->request->get('page');

        $page = $page ? $page : 1;
        if (!$eid) {
            return $this->packError('eid', 'eid-empty');
        }

        $entity_follow_service = $this->service('entity_follow');
        $user_follow_service = $this->service('user_follow');
        $current_uid = $this->service('user')->getCurrentUid();

        $followed_set = $entity_follow_service->fetchFollowedSet($eid)->setCountPerPage(5)->setCurrentPage($page);
        $followed_users = [];
        foreach ($followed_set->getitems() as $user_follow) {
            $user = $user_follow->getFollowedUser();
            $item['uid'] = $user->uid;
            $item['user_avt'] = $this->view('module/user-avt', ['user' => $user]);
            $item['user_nick'] = $user->nick;
            $item['is_following'] = $current_uid ? $user_follow_service->isFollowing($user->uid) ? 'followed' : 'unfollow' : 'unfollow';
            $followed_users[] = $item;
        }
        return $this->packitems([
            'page' => $page,
            'followed_users' => $followed_users
        ]);
    }
}
