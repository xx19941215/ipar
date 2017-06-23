<?php
namespace Ipar\Service;

class TagArticleService extends \Gap\Service\ServiceBase
{

    public function search($query = [])
    {
        return $this->repo('tag_article')->search($query);
    }

}