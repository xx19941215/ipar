<?php
namespace Ipar\Dto;

class RqtDto extends \Mars\Dto\EntityDto {

    public $data;
    
    protected $imgs_arr;
    protected $data_arr;
    protected $data_title;
    protected $data_content;
    protected $data_abbr;

    public function countSolution()
    {
        return service('rqt')->countSolution($this->eid);
    }

    public function countSidea()
    {
        return service('rqt')->countSidea($this->eid);
    }

    public function countSinvent()
    {
        return service('rqt')->countSinvent($this->eid);
    }

    public function countSproduct()
    {
        return service('rqt')->countSproduct($this->eid);
    }

    public function getData()
    {
        return json_decode($this->data);
    }

    public function getDataArr()
    {
        if ($this->data_arr) {
            return $this->data_arr;
        }
        $this->data_arr = json_decode($this->data, true);
        return $this->data_arr;
    }

    public function getDataTitle()
    {
        if ($this->data_title) {
            return $this->data_title;
        }
        $this->data_title = prop($this->getDataArr(), 'title', '');
        return $this->data_title;
    }

    public function getDataContent()
    {
        if ($this->data_content) {
            return $this->data_content;
        }
        $this->data_content = prop($this->getDataArr(), 'content', '');
        return $this->data_content;
    }

    public function stripDataContent()
    {
        return strip_tags($this->getDataContent());
    }

    public function getDataAbbr()
    {
        if ($this->data_abbr) {
            return $this->data_abbr;
        }
        $this->data_abbr = mb_substr($this->stripDataContent(), 0, 98);
        return $this->data_abbr;
    }

    public function getActionKey()
    {
        return get_action_key($this->action_id);
    }

    public function getImgs()
    {
        if ($this->imgs_arr) {
            return $this->imgs_arr;
        }
        $this->imgs_arr = json_decode($this->imgs, true);
        return $this->imgs_arr;
    }

    public function countComment()
    {
        return service('entity_comment')->countComment([
            'entity_type_id' => 1,
            'eid' => $this->eid
        ]);
    }
}
