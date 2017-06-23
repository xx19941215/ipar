<?php
namespace Ipar\Cmd;

class ProductRankerCmd extends \Gap\Console\ControllerBase
{
    public function run()
    {
        $db = adapter_manager()->get('default');

        $db->exec('update entity a right join (SELECT *,count(1) as count FROM `property`
            group by product_eid order by count desc) b on a.eid = b.product_eid set a.rank = b.count
        ');

    }
}
