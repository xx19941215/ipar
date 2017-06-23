<?php
namespace Ipar\Cmd;

class UserIndexerCmd extends \Gap\Console\ControllerBase
{
    public function run()
    {
        define('XS_APP_ROOT', config()->get('xs_app_root'));

        $user_set = $this->service('user')
            ->schUserSet()
            ->setCountPerPage(0);

        $xs = new \XS('user');
        $index = $xs->index;

        echo "开始重建索引 ...\n";
        $index->stopRebuild();
        $index->beginRebuild();

        $total_count = $ok_count = $failed_count = 0;

        foreach ($user_set->getItems() as $user) {
            $doc = new \XSDocument();
            $doc->setFields([
                'uid' => $user->uid,
                'avt' => $user->avt,
                'nick' => $user->nick,
                'zcode' => $user->zcode
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
