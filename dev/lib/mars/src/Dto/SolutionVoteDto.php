<?php
namespace Mars\Dto;

class SolutionVoteDto
{
    public $solution_id;
    public $uid;
    public $status;
    public $changed;

    public function getVoteUser()
    {
        return service('user')->getUserByUid($this->uid);
    }
}
