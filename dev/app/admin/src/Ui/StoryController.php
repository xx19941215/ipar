<?php
namespace Admin\Ui;

class StoryController extends AdminControllerBase
{

    protected $story_service;

    public function index()
    {
        $story_set = $this->getStoryService()->search([
            'status' => null
        ]);
        return $this->page('story/index', [
            'story_set' => $story_set,
        ]);
    }

    public function show()
    {
        $this->prepareTargetUrl();
        $param_id = $this->getParam('id');
        $story = $this->getStoryService()->getStoryById($param_id);
        return $this->page('story/show', [
            'story' => $story
        ]);
    }

    protected function getStoryService()
    {
        if ($this->story_service) {
            return $this->story_service;
        }

        $this->story_service = gap_service_manager()->make('story');
        return $this->story_service;
    }

}
