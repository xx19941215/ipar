<?php
namespace Ipar\Cmd;

class ArticleIndexerCmd extends \Gap\Console\ControllerBase
{
    public function run()
    {
        define('XS_APP_ROOT', config()->get('xs_app_root'));

        $article_set = $this->service('article')
            ->schArticleSet()
            ->setCountPerPage(0);

        $xs = new \XS('article');
        $index = $xs->index;

        echo "开始重建索引 ...\n";
        $index->stopRebuild();
        $index->beginRebuild();

        $total_count = $ok_count = $failed_count = 0;

        foreach ($article_set->getItems() as $article) {
            $doc = new \XSDocument();
            $doc->setFields([
                'id' => $article->id,
                'zcode' => $article->zcode,
                'uid' => $article->uid,
                'title' => $article->title,
                'content' => $this->stripContent($article->content),
                'status' => $article->status,
                'created' => $article->created,
                'changed' => $article->changed,
                'locale_id' => $article->original_id
            ]);

            try {
                $total_count++;
                $index->update($doc);
                $ok_count++;
            } catch (\XSException $e) {
                echo "警告：添加第 $total_count 条数据失败 - " . $e->getMessage() . "\n";
                echo $e->getTraceAsString();
                $failed_count++;
            }
        }

        echo "完成索引导入：成功 $ok_count 条，失败 $failed_count 条\n";

        $index->endRebuild();
    }

    public function stripContent($content)
    {
        $striped = trim(strip_tags($content));
        $striped = preg_replace('/[(\xc2\xa0)|\s]+/u', '', $striped);
        return $striped;
    }
}
