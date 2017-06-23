<?php
namespace Mars\Service;

class StoryService extends \Gap\Service\ServiceBase
{
    protected $story_repo;

    public function bootstrap()
    {
        $this->story_repo = gap_repo_manager()->make('story');
    }

    public function create($data)
    {
        return $this->story_repo->create($data);
    }

    public function search($query = [])
    {
        return $this->story_repo->search($query);
    }

    public function getStoryById($id)
    {
        return $this->story_repo->getStoryById($id);
    }

}
