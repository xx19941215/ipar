<?php
namespace Ipar\Rest;

class CompanyProductController extends \Gap\Routing\Controller
{
    public function product()
    {
        $company = $this->service('company')
            ->findOne([
                'gid' => $this->request->query->get('gid')
            ]);

        $products = $this->service('company_product')
            ->searchExistProductSet(['gid' => $company->gid])
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
            $item['dst_eid'] = $product->eid;
            $item['is_like'] = $this->service('entity_like')->isLike(['eid' => $product->eid]) ? 'liked' : '';
            $item['like_text'] = trans('like');
            $item['like_count'] = $this->service('entity_like')->schEntityLikeSet(['eid' => $product->eid])->getItemCount();
            $item['improving_count'] = $this->service('product')->countPimproving($product->eid);
            $item['trans_improving'] = trans('improving');
            $arr[] = $item;
        }

        return $this->packItem('product', $arr);
    }
}