<?php
namespace Admin\Ui;

class CacheController extends AdminControllerBase {
    public function flushAll()
    {
        lang()->cacheFlushAll();
        return 'cache flush all';
    }
}
