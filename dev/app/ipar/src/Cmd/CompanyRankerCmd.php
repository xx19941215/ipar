<?php
namespace Ipar\Cmd;

class CompanyRankerCmd extends \Gap\Console\ControllerBase
{
    public function run()
    {
        $db = adapter_manager()->get('default');

        // table group add rank filed first!!!
        $db->exec('update `group` a right join (SELECT *,count(1) as count FROM `company_product`
            group by gid order by count desc) b on a.gid = b.gid set a.rank = b.count
        ');

    }
}
