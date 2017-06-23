<?php
namespace Mars\Dto;

class TagDto
{
    public $id;
    public $parent_id;
    public $locale_id;
    public $title;
    public $content;
    public $child_count;
    public $dst_count;
    public $vote_total_count;
    public $status;
    public $logo;
    public $uid;

    public function getLogo()
    {
        return json_decode($this->logo, true);
    }

    public function getAbbr()
    {
        $tag_content = $this->stripContent();

        if (isset($tag_content[60])) {
            $tag_content = mb_substr($tag_content, 0, 60) . '...';
        }
        return $tag_content;
    }

    public function stripContent()
    {
        return trim(strip_tags($this->content));
    }

    /*old code
     * public function getTagMainSet()
    {
        return service('tag')->schTagMainSet(['tag_id' => $this->id]);
    }

    public function getTagTitle($locale_id)
    {
        return service('tag')->fetchTagTitle($this->id, $locale_id);
    }*/
}
