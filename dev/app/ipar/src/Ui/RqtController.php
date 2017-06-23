<?php
namespace Ipar\Ui;


class RqtController extends IparControllerBase {
    public function index()
    {
        $hot_tag = $this->service('product')->getRqtHotTag();
        return $this->page('rqt/index',[
            'hot_tag' => $hot_tag
        ]);
    }

    public function tag()
    {
        $zcode = $this->getParam('zcode');
        $tag_main = service('tag')->findTagMain(['zcode' => $zcode]);
        return $this->page('rqt/tag', ['tag_main' => $tag_main]);
    }

    public function delete()
    {
        $zcode = $this->getParam('zcode');
        $rqt = $this->service('story')->getEtByZcode($zcode);

        return $this->page('rqt/delete', [
            'rqt' => $rqt,
        ]);
    }

    public function deletePost()
    {
        $zcode = $this->getParam('zcode');
        $rqt = $this->service('story')->getEtByZcode($zcode);
        if ($rqt->eid == $this->request->request->get('eid')) {
            $this->service('rqt')->remove($rqt->eid);
            return $this->gotoRoute('rqt-index');
        } else {
            die(trans('unkown-error'));
        }
    }

    public function edit()
    {
        $zcode = $this->getParam('zcode');
        $rqt_service = $this->service('rqt');
        $rqt = $this->service('story')->getEpByZcode('rqt', $zcode);

        assert_mine($rqt);

        $data = (array) $rqt;
        return $this->page('rqt/edit', $data);
    }

    public function editPost()
    {
        $rqt_service = $this->service('rqt');
        $post = $this->request->request;

        $uid = $this->request->getUid();
        $eid = $post->get('eid');
        $title = $post->get('title');
        $attd = $post->get('attd');
        $content = $post->get('content');
        $zcode = $post->get('zcode');

        $rqt_service = $this->service('rqt');

        if ($rqt_service->editRqt(
            $uid,
            $eid,

            $title,
            $content,
            $attd
        )) {
            return $this->gotoRoute(
                'rqt-show',
                ['zcode' => $zcode]
            );
        } else {
            $data['rqt'] = (object) $post->all();
            $data['errors'] = $rqt_service->getErrors();
            return $this->page('rqt/edit', $data);
        }
    }

    public function create()
    {
        return $this->page('rqt/create');
    }

    public function createPost()
    {
        $rqt_service = $this->service('rqt');

        $user = $this->request->getUser();
        $uid = $user->uid;

        $post = $this->request->request;
        $title = $post->get('title');
        $attd = $post->get('attd');
        $content = $post->get('content');
        $src_eid = (int) $post->get('src_eid', 0);


        if ($rqt_service->createRqt(
            $uid,
            $src_eid,
            $title,
            $content,
            $attd
        )) {
            if ($target = $post->get('target')) {
                return $this->gotoUrl($target);
            } else {
                return $this->gotoRoute(
                    'rqt-show',
                    ['zcode' => $rqt_service->lastInsertZcode()]
                );
            }
        } else {
            $data = $post->all();
            $data['errors'] = $rqt_service->getErrors();
            return $this->page('rqt/create', $data);
        }
    }

    public function show()
    {
        $zcode = $this->getParam('zcode');
        $rqt = $this->service('rqt')->getRqtByZcode($zcode);
        $entity_follow = service('entity_follow');
        $eid = $rqt->eid;
        $is_following = $entity_follow->isFollowing($eid);

        $followed_count =$entity_follow->countFollowed($eid);

        $followed_set = $entity_follow->fetchFollowedSet($eid);


        $data['eid'] = $rqt->eid;
        $data['entity_type_id'] = $rqt->type_id;
        $tag_set = gap_service_manager()->make('entity_tag')->search($data);
        return $this->page('rqt/show', [
            'rqt' => $rqt,
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'followed_set' => $followed_set,
            'tag_set' => $tag_set,
        ]);
    }

    public function product()
    {
        $zcode = $this->getParam('zcode');
        $rqt = $this->service('rqt')->getRqtByZcode($zcode);
        $entity_follow = service('entity_follow');
        $eid = $rqt->eid;
        $is_following = $entity_follow->isFollowing($eid);

        $followed_count =$entity_follow->countFollowed($eid);

        $followed_set = $entity_follow->fetchFollowedSet($eid);

        return $this->page('rqt/product', [
            'rqt' => $rqt,
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'followed_set' => $followed_set
        ]);
    }

    public function idea()
    {
        $zcode = $this->getParam('zcode');
        $rqt = $this->service('rqt')->getRqtByZcode($zcode);
        $entity_follow = service('entity_follow');
        $eid = $rqt->eid;
        $is_following = $entity_follow->isFollowing($eid);

        $followed_count =$entity_follow->countFollowed($eid);

        $followed_set = $entity_follow->fetchFollowedSet($eid);

        return $this->page('rqt/idea', [
            'rqt' => $rqt,
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'followed_set' => $followed_set
        ]);
    }

    public function solution()
    {
        $zcode = $this->getParam('zcode');
        $rqt = $this->service('rqt')->getRqtByZcode($zcode);

        return $this->page('rqt/solution', [
            'rqt' => $rqt
        ]);
    }

    public function idea_old()
    {
        $zcode = $this->getParam('zcode');
        $story_service = $this->service('story');
        $rqt = $story_service->getEpByZcode('rqt', $zcode);

        $solutions = $story_service->getEpsBySrcEid('idea', $rqt->eid);
        $cts = $story_service->getCtsBySrcEid($rqt->eid);

        return $this->page(
            'ipar/rqt/show',
            [
                'rqt' => $rqt,
                'solutions' => $solutions,
                'cts' => $cts,
                'is_show_abbr' => true,
                'active' => 'idea'
            ]
        );
    }

    public function show_old()
    {
        $zcode = $this->getParam('zcode');
        $story_service = $this->service('story');
        $rqt = $story_service->getEpByZcode('rqt', $zcode);
        $solutions = $story_service->getEtsBySrcEid($rqt->eid);
        $cts = $story_service->getCtsBySrcEid($rqt->eid);

        $follow_service = $this->service('follow');

        $tags = $this->service('tag')->getTagsByEid($rqt->eid);
        $followers = $follow_service->getFollowersByEid($rqt->eid);

        $follow_service->isFollowingEntities($this->request->getUid(), array_merge($solutions, [$rqt]));

        return $this->page(
            'ipar/rqt/show',
            [
                'rqt' => $rqt,
                'solutions' => $solutions,
                'cts' => $cts,
                'tags' => $tags,
                'followers' => $followers
            ]
        );
    }
}
