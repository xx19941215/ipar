<?php
namespace Mars\Service;

class SolutionVoteService extends MarsServiceBase
{
    public function vote($solution_id)
    {
        $solution_id = (int)$solution_id;
        $key = "voteNumber-$solution_id";
        if ($this->cache()->get($key)) {
            $this->cache()->incr($key);
        }
        return $this->repo('solution_vote')->vote($solution_id);
    }

    public function unvote($solution_id)
    {
        $solution_id = (int)$solution_id;
        $key = "voteNumber-$solution_id";
        if ($this->cache()->get($key)) {
            $this->cache()->incrBy($key, -1);
        }
        return $this->repo('solution_vote')->unvote($solution_id);
    }

    public function isVote($solution_id)
    {
        return $this->repo('solution_vote')->findSolutionVote(['solution_id' => $solution_id]);
    }

    public function schVoteUserSet($query = [])
    {
        return $this->repo('solution_vote')->schVoteUserSet($query);
    }

    public function isVoted($property_id)
    {
        return $this->repo('property_vote')->findPropertyVote(['property_id' => $property_id]);
    }

    public function countVoteUser($sid)
    {
        $key = "voteNumber-$sid";
        if($voteNumber = $this->cache()->get($key)){
            return $voteNumber;
        }
        $voteNumber = $this->repo('solution_vote')->countVoteUser($sid);
        $this->cache()->set($key,$voteNumber, 3600);
        return $voteNumber;
    }
}
