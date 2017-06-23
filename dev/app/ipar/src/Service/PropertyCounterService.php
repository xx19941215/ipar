<?php
namespace Ipar\Service;

class PropertyCounterService extends IparServiceBase
{
    public function countSolved()
    {
        $cache_key = 'count-solved';

        if ($count = $this->cache()->get($cache_key)) {
            return $count;
        }

        $count = $this->repo('property_counter')->countSolved();
        $this->cache()->set($cache_key, $count, 3600);

        return $count;
    }

    public function countImproving()
    {
        $cache_key = 'count-improving';

        if ($count = $this->cache()->get($cache_key)) {
            return $count;
        }

        $count = $this->repo('property_counter')->countImproving();
        $this->cache()->set($cache_key, $count, 3600);

        return $count;
    }
}
