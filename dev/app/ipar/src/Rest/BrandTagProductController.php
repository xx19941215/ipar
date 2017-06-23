<?php
namespace Ipar\Rest;

class BrandTagProductController extends \Gap\Routing\Controller
{
    public function show()
    {
        $products = $this->service('brand_tag_product')
            ->getBrandTagProductSet(['brand_tag_id' => $this->request->query->get('company_brand_tag_id')])
            ->setCountPerPage(12)
            ->setCurrentPage($this->request->query->get('page'))
            ->getItems();

        $arr = [];

        foreach ($products as $product) {
            $item = [];
            $user = user($product->uid);
            if (!$user) {
                continue;
            }

            $item['zcode'] = $product->zcode;
            $item['title'] = title_text($product->getTitle(), 25);
            if ($imgs = $product->getImgs()) {
        $item['html_cover'] = '<img src="' . img_src($imgs[0], 'cover') . '">';
            }
            $item['url'] = route_url('ipar-product-show', ['zcode' => $product->zcode]);

            $item['html_user'] = implode([
                '<a href="', $user->getUrl(), '">',
                $product->uid ? $user->nick : '',
                '</a>',
            ]);

            $item['like_count'] = $product->countLike();
            $item['eid'] = $product->eid;
            $item['is_like'] = $this->service('entity_like')->isLike(['eid' => $product->eid]) ? 'liked' : '';
            $item['like_text'] = trans('like');
            $item['like_count'] = $this->service('entity_like')->schEntityLikeSet(['eid' => $product->eid])->getItemCount();

            $arr[] = $item;
        }

        return $this->packItem('product', $arr);
    }
}