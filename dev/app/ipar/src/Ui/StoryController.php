<?php
namespace Ipar\Ui;

class StoryController extends IparControllerBase {

    public function index()
    {
        $story_set = $this->service('story')->getStorySet();
        return $this->page('story/index', [
            'story_set' => $story_set
        ]);
    }

    public function rqt()
    {
        $story_set = $this->service('story')->getStorySet(['type' => 'rqt']);
        return $this->page('story/rqt', [
            'story_set' => $story_set
        ]);
    }

    public function product()
    {
        $story_set = $this->service('story')->getStorySet(['type' => 'product']);
        return $this->page('story/product', [
            'story_set' => $story_set
        ]);
    }
}
