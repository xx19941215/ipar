<?php
namespace Ipar\Cmd;

class ArticleExtractImgsCmd extends \Gap\Console\ControllerBase
{
    public function run()
    {
        $this->service('article')->contentExtractImgs();
        echo '文章imgs字段填充完毕!';
    }
}
