<?php
namespace Ipar\Rest;

class TagProductController extends \Gap\Routing\Controller
{
    public function index()
    {
        $page = $this->request->query->get('page');
        $zcode = $this->request->query->get('zcode');
        $entity_type_id = 3;
        $products = $this->service('tag_entity')
            ->search(['tag_zcode' => $zcode, 'entity_type_id' => $entity_type_id])
            ->setCurrentPage($page)
            ->setCountPerPage(6)
            ->getItems();
        $arr = $this->getProductItem($products);

        return $this->packItem('product', $arr);
    }

    protected function getProductItem($products)
    {
        $arr = [];

        foreach ($products as $product) {
            $item = [];
            $user = user($product->uid);

            $item['zcode'] = $product->zcode;
            $item['title'] = title_text($product->getTitle(), 25);
            $item['dst_eid'] = $product->eid;
            if ($imgs = $product->getImgs()) {
                $item['html_cover'] = '<img src="' . img_src($imgs[0], 'cover') . '">';
            }
            $item['url'] = route_url('ipar-product-show', ['zcode' => $product->zcode]);

            $item['html_user'] = implode([
                '<a href="', $user->getUrl(), '">',
                $product->uid ? $user->nick : '',
                '</a>',
            ]);
            $item['is_like'] = $this->service('entity_like')->isLike(['eid'=> $product->eid]) ? 'liked' : '';
            $item['like_text'] = trans('like');
            $item['like_count'] = $product->countLike();
            $item['improving_count'] = $this->service('product')->countPimproving($product->eid);
            $item['trans_improving'] = trans('improving');
            $arr[] = $item;
        }
        return $arr;
    }

}