<?php
namespace Ipar\Cmd;

class RqtRankerCmd extends \Gap\Console\ControllerBase
{
    public function run()
    {
        $db = adapter_manager()->get('default');


        $db->exec('update entity a right join (SELECT *,count(1) as count FROM `solution`
            group by rqt_eid order by count desc) b on a.eid = b.rqt_eid set a.rank = b.count
        ');

    }
}
