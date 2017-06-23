<?php
namespace Ipar\Ui;

class ActivityController extends IparControllerBase
{
    public function index()
    {
        $module_set = $this->service('activity')->getActivitySetPreview();
        foreach ($module_set['data_detail'] as $v) {
            $products[] = service('product')->getProductByEid($v->product_id);
        }
        return $this->page(
            'activity/index',
            [
                'set' => $module_set,
                'products' => $products
            ]
        );
    }
}
