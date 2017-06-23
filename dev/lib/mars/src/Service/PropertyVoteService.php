<?php
namespace Mars\Service;

class PropertyVoteService extends MarsServiceBase
{
    public function vote($property_id)
    {
        $property_id = (int)$property_id;
        $key = "voteNumber-$property_id";
        if ($this->cache()->get($key)) {
            $this->cache()->incr($key);
        }
        return $this->repo('property_vote')->vote($property_id);
    }

    public function unvote($property_id)
    {
        $property_id = (int)$property_id;
        $key = "voteNumber-$property_id";
        if ($this->cache()->get($key)) {
            $this->cache()->incrBy($key, -1);
        }
        return $this->repo('property_vote')->unvote($property_id);
    }

    public function isVoted($property_id)
    {
        return $this->repo('property_vote')->findPropertyVote(['property_id' => $property_id]);
    }

    public function schVoteUserSet($query = [])
    {
        return $this->repo('property_vote')->schVoteUserSet($query);
    }

    public function countVoteUser($pid)
    {
        $key = "voteNumber-$pid";

        if ($voteNumber = $this->cache()->get($key)) {
            return $voteNumber;
        }
        $voteNumber = $this->repo('property_vote')->countVoteUser($pid);
        $this->cache()->set($key, $voteNumber, 3600);
        return $voteNumber;
    }
}
