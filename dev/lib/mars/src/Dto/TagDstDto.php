<?php
namespace Mars\Dto;

class TagDstDto
{
    public $id;
    public $dst_type;
    public $dst_id;
    public $tag_id;
    public $vote_count;
    public $changed;

    public function getTagMain()
    {
        return service('tag')->fetchTagMain($this->tag_id, translator()->getLocaleId());
    }

    public function getTagTitle()
    {
        return service('tag')->fetchTagTitle($this->tag_id, translator()->getLocaleId());
    }

    public function countVote()
    {
        return $this->vote_count;
    }

    public function getVoteSet()
    {
        return service('tag')->schTagDstVoteSet(['tag_dst_id' => $this->id]);
    }

    public function hasVoted()
    {
        return service('tag')->hasVoted($this->id);
    }
}
