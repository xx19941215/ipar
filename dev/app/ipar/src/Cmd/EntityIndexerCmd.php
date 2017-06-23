<?php
namespace Ipar\Cmd;

class EntityIndexerCmd extends \Gap\Console\ControllerBase
{
    public function run()
    {
        define('XS_APP_ROOT', config()->get('xs_app_root'));

        $entity_set = $this->service('entity')
            ->schEntitySet()
            ->setCountPerPage(0);

        $xs = new \XS('entity');
        $index = $xs->index;

        echo "开始重建索引 ...\n";
        $index->stopRebuild();
        $index->beginRebuild();

        $total_count = $ok_count = $failed_count = 0;

        foreach ($entity_set->getItems() as $entity) {
            $doc = new \XSDocument();
            $doc->setFields([
                'eid' => $entity->eid,
                'type_id' => $entity->type_id,
                'zcode' => $entity->zcode,
                'owner_uid' => $entity->owner_uid,
                'uid' => $entity->uid,
                'title' => $entity->title,
                'content' => $entity->stripContent(),
                'rank' => $entity->rank,
                'status' => $entity->status,
                'imgs' => $entity->imgs,
                'created' => $entity->created,
                'changed' => $entity->changed
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
}
