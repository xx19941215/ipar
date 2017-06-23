<?php
namespace Ipar\Ui;

class FeatureController extends IparControllerBase {
    public function index()
    {
        $page_index = (int) $this->request->query->get('page');
        $features = $this->service('story')->getLatestEpsByType('feature', $page_index);

        return $this->page('feature/index', [
            'features' => $features
        ]);
    }

    public function delete()
    {
        $zcode = $this->getParam('zcode');
        $feature = $this->service('story')->getEtByZcode($zcode);

        return $this->page('feature/delete', [
            'feature' => $feature,
        ]);
    }

    public function deletePost()
    {
        $zcode = $this->getParam('zcode');
        $feature = $this->service('story')->getEtByZcode($zcode);
        if ($feature->eid == $this->request->request->get('eid')) {
            $this->service('feature')->remove($feature->eid);
            return $this->gotoRoute('feature-index');
        } else {
            die(trans('unkown-error'));
        }
    }

    public function edit()
    {
        $zcode = $this->getParam('zcode');
        $feature_service = $this->service('feature');
        $feature = $this->service('story')->getEpByZcode('feature', $zcode);

        assert_mine($feature);

        $data = (array) $feature;
        return $this->page('feature/edit', $data);
    }

    public function editPost()
    {
        $feature_service = $this->service('feature');
        $post = $this->request->request;

        $uid = $this->request->getUid();
        $eid = $post->get('eid');
        $title = $post->get('title');
        $attd = $post->get('attd');
        $content = $post->get('content');
        $zcode = $post->get('zcode');

        $feature_service = $this->service('feature');

        if ($feature_service->editFeature(
            $uid,
            $eid,

            $title,
            $content,
            $attd
        )) {
            return $this->gotoRoute(
                'feature-show',
                ['zcode' => $zcode]
            );
        } else {
            $data['feature'] = (object) $post->all();
            $data['errors'] = $feature_service->getErrors();
            return $this->page('feature/edit', $data);
        }
    }

    public function create()
    {
        return $this->page('feature/create');
    }

    public function createPost()
    {
        $feature_service = $this->service('feature');

        $uid = $this->request->getUid();

        $post = $this->request->request;
        $title = $post->get('title');
        $attd = $post->get('attd');
        $content = $post->get('content');
        $src_eid = (int) $post->get('src_eid', 0);


        if ($feature_service->createFeature(
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
                    'feature-show',
                    ['zcode' => $feature_service->lastInsertZcode()]
                );
            }
        } else {
            $data = $post->all();
            $data['errors'] = $feature_service->getErrors();
            return $this->page('feature/create', $data);
        }
    }

    public function show()
    {
        $zcode = $this->getParam('zcode');
        return $this->page('feature/show', [
            'feature' => $this->service('feature')->getFeatureByZcode($zcode)
        ]);

        $zcode = $this->getParam('zcode');
        $story_service = $this->service('story');
        $feature = $story_service->getEpByZcode('feature', $zcode);
        $solutions = $story_service->getEtsBySrcEid($feature->eid);
        $cts = $story_service->getCtsBySrcEid($feature->eid);

        return $this->page(
            'ipar/feature/show',
            [
                'feature' => $feature,
                'solutions' => $solutions,
                'cts' => $cts
            ]
        );
    }

    public function product()
    {
        return '';
    }

    public function idea()
    {
        $zcode = $this->getParam('zcode');
        $story_service = $this->service('story');
        $feature = $story_service->getEpByZcode('feature', $zcode);

        $solutions = $story_service->getEpsBySrcEid('idea', $feature->eid);
        $cts = $story_service->getCtsBySrcEid($feature->eid);

        return $this->page(
            'ipar/feature/show',
            [
                'feature' => $feature,
                'solutions' => $solutions,
                'cts' => $cts,
                'is_show_abbr' => true,
                'active' => 'idea'
            ]
        );
    }

}
