<?php
namespace Ipar\Ui;

class ProductController extends IparControllerBase
{
    public function index()
    {
        $hot_tag = $this->service('product')->getProductHotTag();
        return $this->page('product/index', [
            'hot_tag' => $hot_tag
        ]);
    }

    public function tag()
    {
        $tag_main = service('tag')->findTagMain([
            'zcode' => $this->getParam('zcode')
        ]);
        if ($tag_main) {
            return $this->page('product/tag', ['tag_main' => $tag_main]);
        }

        die('not-found');
        //todo
    }

    public function brand()
    {
        $brand_main = service('brand')->findBrandMain([
            'zcode' => $this->getParam('zcode')
        ]);

        if ($brand_main) {
            return $this->page('product/brand', ['brand_main' => $brand_main]);
        }

        die('not-found');
        // todo
    }

    public function all()
    {
        $product_set = $this->service('product')->getProductSet();
        return $this->page('product/all', [
            'product_set' => $product_set
        ]);
    }

    public function delete()
    {
        $zcode = $this->getParam('zcode');
        $product = $this->service('story')->getEtByZcode($zcode);

        return $this->page('product/delete', [
            'product' => $product,
        ]);
    }

    public function deletePost()
    {
        $zcode = $this->getParam('zcode');
        $product = $this->service('story')->getEtByZcode($zcode);
        if ($product->eid == $this->request->request->get('eid')) {
            $this->service('product')->remove($product->eid);
            return $this->gotoRoute('product-index');
        } else {
            die(trans('unkown-error'));
        }
    }

    public function edit()
    {
        $zcode = $this->getParam('zcode');
        $product_service = $this->service('product');
        $product = $this->service('story')->getEpByZcode('product', $zcode);

        assert_mine($product);

        $data = (array)$product;
        return $this->page('product/edit', $data);
    }

    public function editPost()
    {
        $post = $this->request->request;
        $uid = $this->request->getUid();
        $eid = $post->get('eid');
        $title = $post->get('title');
        $attd = $post->get('attd');
        $content = $post->get('content');
        $zcode = $post->get('zcode');
        $product_service = $this->service('product');

        if ($product_service->editProduct(
            $uid,
            $eid,
            $title,
            $content,
            $attd
        )
        ) {
            return $this->gotoRoute(
                'product-show',
                ['zcode' => $zcode]
            );
        } else {
            $data['product'] = (object)$post->all();
            $data['errors'] = $product_service->getErrors();
            return $this->page('product/edit', $data);
        }
    }

    public function create()
    {
        return $this->page('product/create');
    }

    public function createPost()
    {
        $product_service = $this->service('product');

        $user = $this->request->getUser();
        $uid = $user->uid;

        $post = $this->request->request;
        $title = $post->get('title');
        $attd = $post->get('attd');
        $content = $post->get('content');
        $src_eid = (int)$post->get('src_eid', 0);


        if ($product_service->createProduct(
            $uid,
            $src_eid,
            $title,
            $content,
            $attd
        )
        ) {
            if ($target = $post->get('target')) {
                return $this->gotoUrl($target);
            } else {
                return $this->gotoRoute(
                    'product-show',
                    ['zcode' => $product_service->lastInsertZcode()]
                );
            }
        } else {
            $data = $post->all();
            $data['errors'] = $product_service->getErrors();
            return $this->page('product/create', $data);
        }
    }

    public function feature()
    {
        $zcode = $this->getParam('zcode');
        $product = $this->service('product')->getProductByZcode($zcode);
        $entity_follow = service('entity_follow');
        $eid = $product->eid;
        $is_following = $entity_follow->isFollowing($eid);
        $followed_count = $entity_follow->countFollowed($eid);
        $followed_set = $entity_follow->fetchFollowedSet($eid);

        $query['eid'] = $product->eid;
        $query['entity_type_id'] = $product->type_id;
        $tag_set = gap_service_manager()->make('entity_tag')->search($query);

        $product_purchase_set = $this->service('product_purchase')->search(['eid' => $eid]);
        $company_set = $this->service('company_product')->searchExistCompanySet(['eid' => $product->eid]);
        $brand_tag = $this->service('brand_tag_product')->productGetBrandTag(['eid' => $product->eid]);

        return $this->page('product/show', [
            'product' => $product,
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'followed_set' => $followed_set,
            'tag_set' => $tag_set,
            'product_purchase_set' => $product_purchase_set,
            'company_set' => $company_set,
            'brand_tag' => $brand_tag
        ]);

    }

    /*
    public function property()
    {
    }

    public function feature()
    {
        $zcode = $this->getParam('zcode');
        $product = $this->service('product')->getProductByZcode($zcode);

        return $this->page('product/feature', [
            'product' => $product
        ]);
    }
     */

    public function solved()
    {
        $activity_product_list = [];
        $activity_advice_banner_url = '';
        $zcode = $this->getParam('zcode');
        $product = $this->service('product')->getProductByZcode($zcode);
        $entity_follow = service('entity_follow');
        $eid = $product->eid;
        $is_following = $entity_follow->isFollowing($eid);

        $followed_count = $entity_follow->countFollowed($eid);
        $followed_set = $entity_follow->fetchFollowedSet($eid);

        $query['eid'] = $product->eid;
        $query['entity_type_id'] = $product->type_id;
        $tag_set = gap_service_manager()->make('entity_tag')->search($query);

        $product_purchase_set = $this->service('product_purchase')->search(['eid' => $eid]);
        $company_set = $this->service('company_product')->searchExistCompanySet(['eid' => $product->eid]);
        //$entity_type_key = get_type_key($this->product->type_id);
        $brand_tag = $this->service('brand_tag_product')->productGetBrandTag(['eid' => $product->eid]);

        $activity_date = $this->service('activity')->getActivityDate();
        $date = date('Y-m-d H:i:s');
        if (($activity_date && strtotime($date) - strtotime($activity_date->created) > 0))
        {
            $activity_advice_banner_url = $this->service('activity')->getActivityIndexImg();
            $activity_product_list = $this->service('activity_product')->getActivityProductList();
        }

        $activity_advice = 0;
        foreach ($activity_product_list as $v) {
            if ($v->product_id == $eid) {
                $activity_advice = 1;
            }
        }

        return $this->page('product/solved', [
            'product' => $product,
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'followed_set' => $followed_set,
            'tag_set' => $tag_set,
            'product_purchase_set' => $product_purchase_set,
            'company_set' => $company_set,
            'brand_tag' => $brand_tag,
            'activity_advice_banner_url' => $activity_advice_banner_url,
            'activity_advice' => $activity_advice
        ]);
    }

    public function show()
    {
        $activity_product_list = [];
        $activity_advice_banner_url = '';
        $zcode = $this->getParam('zcode');
        $product = $this->service('product')->getProductByZcode($zcode);
        $entity_follow = service('entity_follow');
        $eid = $product->eid;
        $is_following = $entity_follow->isFollowing($eid);

        $followed_count = $entity_follow->countFollowed($eid);
        $followed_set = $entity_follow->fetchFollowedSet($eid);

        $query['eid'] = $product->eid;
        $query['entity_type_id'] = $product->type_id;
        $tag_set = gap_service_manager()->make('entity_tag')->search($query);

        $product_purchase_set = $this->service('product_purchase')->search(['eid' => $eid]);
        $company_set = $this->service('company_product')->searchExistCompanySet(['eid' => $product->eid]);
        $brand_tag = $this->service('brand_tag_product')->productGetBrandTag(['eid' => $product->eid]);


        $activity_date = $this->service('activity')->getActivityDate();
        $date = date('Y-m-d H:i:s');
        if ($activity_date && (strtotime($date) - strtotime($activity_date->created) > 0))
        {
            $activity_advice_banner_url = $this->service('activity')->getActivityIndexImg();
            $activity_product_list = $this->service('activity_product')->getActivityProductList();
        }

        $activity_advice = 0;
        foreach ($activity_product_list as $v) {
            if ($v->product_id == $eid) {
                $activity_advice = 1;
            }
        }

        return $this->page('product/improving', [
            'product' => $product,
            'is_following' => $is_following,
            'followed_count' => $followed_count,
            'followed_set' => $followed_set,
            'tag_set' => $tag_set,
            'product_purchase_set' => $product_purchase_set,
            'company_set' => $company_set,
            'brand_tag' => $brand_tag,
            'activity_advice_banner_url' => $activity_advice_banner_url,
            'activity_advice' => $activity_advice
        ]);
    }
}
