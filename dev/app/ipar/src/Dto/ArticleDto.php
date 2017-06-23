<?php
namespace Ipar\Dto;

class ArticleDto {
    public $id;
    public $title;
    public $uid;
    public $locale_id;
    public $imgs;
    public $abbr;

    protected $imgs_arr;

    public function getUser()
    {
        return user($this->uid);
    }

    public function getAbbr()
    {
        $striped = $this->stripContent();
        if(isset($striped[98])){
            $striped = mb_substr($striped,0,98).'...';
        }
        return $striped;
    }

    public function stripContent()
    {
        $striped = trim(strip_tags($this->content));
        $striped = preg_replace('/[(\xc2\xa0)|\s]+/u', '', $striped);
        //bin2hex()
        //http://www.cnblogs.com/kuyuecs/archive/2011/04/15/1689000.html
        return $striped;
    }

    public function getExerpt()
    {
        $str = $this->content;
        $str = strip_tags($str);

        if ($this->locale_id == 1) {
            $str = mb_substr($str, 0, 100);
        } else {
            $str = mb_substr($str, 0, 150);
        }

        $str = str_replace(array("\r", "\n"), '', $str);

        $str = $str . '...';

        return $str;
    }

    public function countComment()
    {
        return service('article_comment')->countComment([
            'article_id' => $this->id
        ]);
    }

    public function getImgs()
    {
        if ($this->imgs_arr) {
            return $this->imgs_arr;
        }
        $this->imgs_arr = json_decode($this->imgs, true);
        return $this->imgs_arr;
    }

}
