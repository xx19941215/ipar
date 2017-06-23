<?php
namespace Ipar\Search\Dto;

class UserSearchDto extends IparSearchDtoBase
{
    public $uid;
    public $nick;
    public $avt;
    public $avt_arr;
    public $zcode;

    protected $highlights = [
        'title' => 1
    ];

    public function getAvt($size = 'small')
    {
        if ($this->avt_arr) {
            return $this->avt_arr;
        }
        if ($this->avt) {
            $this->avt_arr = json_decode($this->avt, true);
            return $this->avt_arr;
        }

        return false;
    }

    public function getUrl()
    {
        return route_url('ipar-i-home', ['zcode' => $this->zcode]);
    }

}
