<?php
namespace Ipar\Rest;

class VoteController extends \Gap\Routing\Controller
{
    public function voteSolutionPost()
    {
        $sid = $this->request->request->get('solution_id');
        $vote = $this->service('solution_vote')->vote($sid);
        if (!$vote->isOk()) {
            return $vote;
        }
        $dst_eid = $vote->getItems('dst_eid');
        $like = $this->service('entity_like')->like($dst_eid['dst_eid']);
        return $like;
    }

    public function unvoteSolutionPost()
    {
        $sid = $this->request->request->get('solution_id');
        $vote = $this->service('solution_vote')->unvote($sid);
        if (!$vote->isOk()) {
            return $vote;
        }

        $dst_eid = $vote->getItems('dst_eid');
        $like = $this->service('entity_like')->unlike($dst_eid['dst_eid']);
        return $like;
    }

    public function getSolutionVoteUsers()
    {
        $sid = $this->request->request->get('solution_id');
        $page = $this->request->request->get('page');
        $page = $page ? $page : 1;

        $user_set = $this->service('solution_vote')->schVoteUserSet(['sid' => $sid]);
        $users = [];
        $user_follow_service = $this->service('user_follow');
        foreach ($user_set->getitems() as $user_like) {
            $user = $user_like->getVoteUser();
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

    public function votePropertyPost()
    {
        $property_id = $this->request->request->get('property_id');
        $vote = $this->service('property_vote')->vote($property_id);
        $dst_eid = $vote->getItems('dst_eid');
        if ($vote->isOk()) {
            $like = $this->service('entity_like')->like($dst_eid['dst_eid']);
        }
        return $vote;
    }

    public function unvotePropertyPost()
    {
        $property_id = $this->request->request->get('property_id');
        $vote = $this->service('property_vote')->unvote($property_id);
        $dst_eid = $vote->getItems('dst_eid');
        if ($vote->isOk()) {
            //to du unlike
            // $like = $this->service('entity_like')->unlike($dst_eid['dst_eid']);
        }
        return $vote;
    }

    public function getPropertyVoteUsers()
    {
        $property_id = $this->request->request->get('property_id');
        $page = $this->request->request->get('page');

        $page = $page ? $page : 1;

        $user_set = $this->service('property_vote')->schVoteUserSet(['property_id' => $property_id]);
        $users = [];
        $user_follow_service = $this->service('user_follow');
        foreach ($user_set->getitems() as $user_like) {
            $user = $user_like->getVoteUser();
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
