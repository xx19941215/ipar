<?php
namespace Ipar\Ui;

class ProductPurchaseController extends IparControllerBase
{
    public function go()
    {
        $product_purchase_id = $this->getParam('product_purchase_id');
        $product_purchase = $this->service('product_purchase')->findOne(['id' => $product_purchase_id]);
        $data = [
            'product_purchase_id' => (int)$product_purchase_id,
            'uid' => current_uid(),
            'client_ip' => $this->request->getClientIp(),
            'referer' => $this->request->headers->get('referer'),
            'user_agent' => $this->request->headers->get('user-agent')
        ];
        $pack = $this->service('product_purchase_statistic')->create($data);

        return $this->gotoUrl($product_purchase->purchase_url);
    }
}