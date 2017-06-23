<?php

namespace Ipar\Service;

class SolutionService extends \Mars\Service\EntityService
{
    protected $solution_repo;

    public function bootstrap()
    {
        $this->solution_repo = $this->repo('solution_repo');
        parent::bootstrap();
    }

    public function isVoted($solution_id,$uid){
        return $this->solution_repo->ifVoted($solution_id,$uid);
    }
}
