<?php
namespace Ipar\Rest;

use Gap\Routing\Controller as RoutingController;

class GroupFollowController extends RoutingController
{
    public function followGroupPost()
    {
        $gid = $this->request->request->get('gid');
        if (!$gid) {
            return $this->packError('gid', 'empty');
        }
        $group_service = service('group_follow');
        $follow = $group_service->follow($gid);
        if (!$follow->isOk()) {
            return $follow;
        }
        $followed_set = $group_service->fetchFollowedSet($gid);
        $followed_users = [];
        foreach ($followed_set->getitems() as $user_follow) {
            $user = $user_follow->getFollowedUser();
            $followed_users[] = $this->view('module/user-avt', ['user' => $user]);
        }
        return $this->packitems([
            'is_following' => $group_service->isFollowing(['gid' => $gid]),
            'followed_count' => $followed_set->getItemCount(),
            'followed_users' => $followed_users
            ]);
    }

    public function unfollowGroupPost()
    {
        $gid = $this->request->request->get('gid');
        if (!$gid) {
            return $this->packError('gid', 'empty');
        }
        $group_service = service('group_follow');
        $unfollow = $group_service->unfollow($gid);
        if (!$unfollow->isOk()) {
            return $unfollow;
        }
        $followed_set = $group_service->fetchFollowedSet($gid);
        $followed_users = [];
        foreach ($followed_set->getitems() as $user_follow) {
            $user = $user_follow->getFollowedUser();
            $followed_users[] = $this->view('module/user-avt', ['user' => $user]);
        }
        return $this->packitems([
            'is_following' => $group_service->isFollowing(['gid' => $gid]),
            'followed_count' => $followed_set->getItemCount(),
            'followed_users' => $followed_users
            ]);
    }
}
