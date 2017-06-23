<?php
namespace Ipar\Ui;

use Gap\File\Image as FileImage;

class IController extends IparControllerBase
{
    public function bootstrap()
    {
        if ($zcode = $this->getParam('zcode')) {
            $this->user = user_service()->getUserByZcode($zcode);
            /*
            if (!$this->user) {
                return $this->badRequest();
            }
            $this->tags = $this->service('tag')->getTagsByUid($this->user->uid);
             */
        }
    }

    public function index()
    {
        $user = current_user();
        if ($user) {
            return $this->gotoRoute('ipar-i-home', ['zcode' => $user->zcode]);
        } else {
            return $this->gotoRoute('login');
        }
    }

    public function home()
    {
        $userfollow = $this->service('user_follow');

        $uid = $this->user->uid;
        $is_following = $userfollow->isFollowing($uid);

        $followed_count = $userfollow->countFollowed($uid);

        $following_count = $userfollow->countFollowing($uid);


        return $this->page('i/home', [
            'user' => $this->user,
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'following_count' => $following_count
        ]);
    }

    public function rqt()
    {
        $userfollow = $this->service('user_follow');

        $uid = $this->user->uid;
        $is_following = $userfollow->isFollowing($uid);

        $followed_count = $userfollow->countFollowed($uid);

        $following_count = $userfollow->countFollowing($uid);
        return $this->page('i/rqt', [
            'user' => $this->user,
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'following_count' => $following_count
        ]);
    }

    public function feature()
    {
        $userfollow = $this->service('user_follow');

        $uid = $this->user->uid;
        $is_following = $userfollow->isFollowing($uid);

        $followed_count = $userfollow->countFollowed($uid);

        $following_count = $userfollow->countFollowing($uid);
        return $this->page('i/feature', [
            'user' => $this->user,
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'following_count' => $following_count
        ]);
    }

    public function product()
    {
        $userfollow = $this->service('user_follow');

        $uid = $this->user->uid;
        $is_following = $userfollow->isFollowing($uid);

        $followed_count = $userfollow->countFollowed($uid);

        $following_count = $userfollow->countFollowing($uid);
        return $this->page('i/product', [
            'user' => $this->user,
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'following_count' => $following_count
        ]);
    }

    public function idea()
    {
        $entities = $this->service('story')->getEpsByUid('idea', $this->user->uid);
        return $this->getHomePage($entities, 'idea');
    }

    public function invent()
    {
        $entities = $this->service('story')->getEpsByUid('invent', $this->user->uid);
        return $this->getHomePage($entities, 'invent');
    }


    public function sketch()
    {
        $entities = $this->service('story')->getEpsByUid('sketch', $this->user->uid);
        return $this->getHomePage($entities, 'sketch');
    }

    public function appearance()
    {
        $entities = $this->service('story')->getEpsByUid('appearance', $this->user->uid);
        return $this->getHomePage($entities, 'appearance');
    }

    protected function getHomePage($entities, $active = 'home')
    {
        $by_uid = $this->request->getUid();

        $follow_service = $this->service('follow');
        $follow_service->isFollowingEntities($by_uid, $entities);
        $follow_service->isFollowingUsers($by_uid, [$this->user]);

        $followers = $follow_service->getFollowersByUid($this->user->uid);
        $following_users = $follow_service->getFollowingUsers($this->user->uid);
        $following_users_ct = $follow_service->getFollowingUsersCt($this->user->uid);

        return $this->page('ipar/i/home.phtml', [
            'user' => $this->user,
            'tags' => $this->tags,
            'entities' => $entities,
            'active' => $active,
            'followers' => $followers,
            'following_users' => $following_users,
            'following_users_ct' => $following_users_ct
        ]);
    }

    public function uploadAvtPost()
    {
        if (!$img_file = $this->request->files->get('img')) {
            return $this->badRequest();
        }
        $config = $this->kernel->getConfig();
        $post = $this->request->request;

        $img_tool = new FileImage(
            $config->get('img.base_url'),
            $config->get('img.base_dir')
        );

        $img_opts = $img_tool->save($img_file);

        $src = [
            'x' => $post->get('src_x', 0),
            'y' => $post->get('src_y', 0),
            'w' => $post->get('src_w', 0),
            'h' => $post->get('src_h', 0)
        ];

        $small_dst = ['w' => 42, 'h' => 42];
        $medium_dst = ['w' => 115, 'h' => 115];

        $img_tool->resize('small', $img_opts, $small_dst, $src);
        $img_tool->resize('medium', $img_opts, $medium_dst, $src);

        $avt = [
            'dir' => parse_url($img_opts['dir_url'], PHP_URL_PATH),
            'name' => $img_opts['base'],
            'ext' => $img_opts['ext']
        ];

        $user_service = user_service();
        if ($user_service->updateAvt($this->request->getUid(), $avt)) {
            $avt['dir_url'] = $img_opts['dir_url'];
            return [
                'ok' => 1,
                'avt' => $avt
            ];
        } else {
            return [
                'ok' => 0,
                'errors' => $user_service->getErrors()
            ];
        }

    }
}
