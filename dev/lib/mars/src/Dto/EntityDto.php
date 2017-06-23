<?php
namespace Mars\Dto;

class EntityDto {
    public $eid;
    public $type_id;
    public $ptype_id;
    public $stype_id;
    public $zcode;
    public $imgs;

    public $owner_uid;
    public $uid;

    public $status;
    public $created;
    public $changed;

    public $title;
    public $content;
    public $abbr;

    public $e_submit_id;

    public $rank;

    protected $_stripped_content;
    protected $_imgs_arr;



    public function getImgs()
    {
        if ($this->_imgs_arr) {
            return $this->_imgs_arr;
        }
        $this->_imgs_arr = json_decode($this->imgs, true);
        return $this->_imgs_arr;
    }

    public function getTitle()
    {
        return $this->title;
        /*
        if ($this->title) {
            return $this->title;
        }
        $this->title = $this->getAbbr();
        return $this->title;
         */
    }

    public function getAbbr()
    {
        if ($this->abbr) {
            return $this->abbr;
        }
        $this->abbr = mb_substr($this->stripContent(), 0, 98);
        return $this->abbr;
    }

    public function getSubmitId()
    {
        if ($this->e_submit_id) {
            return $this->e_submit_id;
        }

        $this->e_submit_id = service('entity')->getLatestSubmitId($this->eid);
        return $this->e_submit_id;

    }

    public function stripContent()
    {
        if ($this->_stripped_content) {
            $this->_stripped_content;
        }

        $this->_stripped_content = strip_tags($this->content);
        return $this->_stripped_content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getTypeKey()
    {
        return get_type_key($this->type_id);
    }

    public function getPtypeKey()
    {
        if ($this->ptype_id) {
            return get_type_key($this->ptype_id);
        }

        return $this->getTypeKey();
    }

    public function getStypeKey()
    {
        if ($this->stype_id) {
            return get_type_key($this->stype_id);
        }
        return $this->getTypeKey();
    }

    public function getTagSet()
    {
        $query['eid'] = $this->eid;
        $query['entity_type_id'] = $this->type_id;
        $tag_set = gap_service_manager()->make('entity_tag')->search($query);
        $tag_set->setCurrentPage(1);
        return $tag_set;
    }

    public function countFollowed()
    {
        return service('entity_follow')->countFollowed($this->eid);
    }
    public function countLike()
    {
        return service('entity')->countLike($this->eid);
    }
    public function isLike()
    {
        return service('entity_like')->isLike(['eid' => $this->eid]);
    }
    public function countComment()
    {
        return service('entity_comment')->countComment([
            'entity_type_id' => $this->type_id,
            'eid' => $this->eid
        ]);
    }
    public function countSubmit()
    {
        return service('entity')->countSubmit($this->eid);
    }
    public function countSrc()
    {
        return service('entity')->countSrc($this->eid);
    }
    public function countStory()
    {
        return service('entity')->countStory($this->eid);
    }

    public function isVoted($property_id,$uid){
        return service('solution')->isVoted($property_id,$uid);
    }

}
