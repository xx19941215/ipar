<?php
namespace Ipar\Service;

class ProductService extends \Mars\Service\EntityService
{

    protected $product_repo;

    public function bootstrap()
    {
        $this->product_repo = $this->repo('product');
        parent::bootstrap();
    }

    public function getProductByEid($eid)
    {
        return $this->product_repo->getProductByEid($eid);
    }

    public function getProductByZcode($zcode)
    {
        return $this->product_repo->getProductByZcode($zcode);
    }

    public function createProduct($title, $content, $url = '')
    {
        if (true !== ($validated = $this->validateProduct($title, $content, $url))) {
            return $validated;
        }
        return $this->product_repo->createProduct($title, $content, $url);
    }

    public function updateProduct($eid, $title, $content, $url = '')
    {
        $eid = (int) $eid;
        if ($eid <= 0) {
            return $this->packError('eid', 'must-be-positive-integer');
        }
        if (true !== ($validated = $this->validateProduct($title, $content, $url))) {
            return $validated;
        }
        //$this->deleteCachedEntity($eid);
        return $this->product_repo->updateProduct($eid, $title, $content, $url);
    }


    // deprecated
    public function deleteProduct($eid)
    {
        return $this->deleteProductByEid($eid);
    }

    public function deleteProductByEid($eid)
    {
        $eid = (int) $eid;
        if (!$eid) {
            return $this->packError('eid', 'must-be-positive-integer');
        }

        //$this->deleteCachedEntity($eid);
        return $this->product_repo->deleteProductByEid($eid);
    }


    public function schProductSet($opts = [])
    {
        $opts['type_key'] = 'product';
        return $this->product_repo->schProductSet($opts);
    }


    /*
     * Create Property
     */

    public function createProperty($product_eid, $ptype_key, $dst_eid)
    {
        $product_eid = (int) $product_eid;
        $dst_eid = (int) $dst_eid;

        return $this->product_repo->createProperty($product_eid, $ptype_key, $dst_eid);
    }

    public function createPsolved($product_eid, $title, $content)
    {
        if (true !== ($validated = $this->validateRqt($title))) {
            return $validated;
        }
        return $this->product_repo->createPsolved($product_eid, $title, $content);
    }

    public function createPimproving($product_eid, $title, $content)
    {
        if (true !== ($validated = $this->validateRqt($title))) {
            return $validated;
        }
        return $this->product_repo->createPimproving($product_eid, $title, $content);
    }

    public function createPfeature($product_eid, $title, $content)
    {
        if (true !== ($validated = $this->validateFeature($title, $content))) {
            return $validated;
        }
        return $this->product_repo->createPfeature($product_eid, $title, $content);
    }

    public function createPbranch($product_eid, $title)
    {
        $product_eid = (int) $product_eid;
        if ($product_eid <= 0) {
            return $this->packError('product_eid', 'product_eid-must-be-positive-integer');
        }
        if (!is_string($title) || empty($title)) {
            return $this->packNotEmpty('title');
        }
        $title_length = mb_strlen($title);
        if ($title_length < 3 || $title_length > 120) {
            return $this->packOutLength('title', 3, 120);
        }

        return $this->product_repo->createPbranch($product_eid, $title);
    }

    public function createPtag($product_eid, $title)
    {
        $product_eid = (int) $product_eid;
        if ($product_eid <= 0) {
            return $this->packError('product_eid', 'product_eid-must-be-positive-integer');
        }
        if (!is_string($title) || empty($title)) {
            return $this->packNotEmpty('title');
        }
        $title_length = mb_strlen($title);
        if ($title_length < 3 || $title_length > 120) {
            return $this->packOutLength('title', 3, 120);
        }

        return $this->product_repo->createPtag($product_eid, $title);
    }

    public function createPtarget($product_eid, $title)
    {
        $product_eid = (int) $product_eid;
        if ($product_eid <= 0) {
            return $this->packError('product_eid', 'product_eid-must-be-positive-integer');
        }
        if (!is_string($title) || empty($title)) {
            return $this->packNotEmpty('title');
        }
        $title_length = mb_strlen($title);
        if ($title_length < 3 || $title_length > 120) {
            return $this->packOutLength('title', 3, 120);
        }

        return $this->product_repo->createPtarget($product_eid, $title);
    }

    //
    // count
    //
    public function countProperty($product_eid)
    {
        return $this->product_repo->countProperty($product_eid);
    }
    public function countPsolved($product_eid)
    {
        return $this->product_repo->countPsolved($product_eid);
    }
    public function countPimproving($product_eid)
    {
        $cache_key = "count-pimproving-{$product_eid}";
        if ($count = $this->cache()->get($cache_key)) {
            return $count;
        }
        $count = $this->product_repo->countPimproving($product_eid);
        $this->cache()->set($cache_key, $count, 3600);
        return $count;
    }
    public function countPfeature($product_eid)
    {
        return $this->product_repo->countPfeature($product_eid);
    }
    /*
     * Search  Property
     */

    public function schPropertySet($product_eid, $opts = [])
    {
        $product_eid = (int) $product_eid;
        return $this->product_repo->schPropertySet($product_eid, $opts);
    }

    public function schPsolvedSet($product_eid, $opts = [])
    {
        $opts['ptype_key'] = 'solved';
        return $this->schPropertySet($product_eid, $opts);
    }

    public function schPimprovingSet($product_eid, $opts = [])
    {
        $opts['ptype_key'] = 'improving';
        return $this->schPropertySet($product_eid, $opts);
    }

    public function schPfeatureSet($product_eid, $opts = [])
    {
        $opts['ptype_key'] = 'feature';
        return $this->schPropertySet($product_eid, $opts);
    }


    public function schPbranchSet($product_eid)
    {
        return $this->product_repo->schPbranchSet($product_eid);
    }

    public function schPtagSet($product_eid)
    {
    }

    public function schPtargetSet($product_eid)
    {
    }

    public function updatePbranch($pbranch_id, $title)
    {
        $pbranch_id = (int) $pbranch_id;
        if ($pbranch_id <= 0) {
            return $this->packError('eid', 'must-be-positive-integer');
        }
        return $this->product_repo->updatePbranch($pbranch_id, $title);
    }

    public function updatePtag($ptag_id, $title)
    {
        $ptag_id = (int) $ptag_id;
        if ($ptag_id <= 0) {
            return $this->packError('ptag_id', 'must-be-positive-integer');
        }
        return $this->product_repo->updatePtag($ptag_id, $title);
    }

    public function updatePtarget($ptarget_id, $title)
    {
        $ptarget_id = (int) $ptarget_id;
        if ($ptarget_id <= 0) {
            return $this->packError('ptarget_id', 'must-be-positive-integer');
        }
        return $this->product_repo->updatePtarget($ptarget_id, $title);
    }

    public function getPbranchById($pbranch_id)
    {
        $pbranch_id = (int) $pbranch_id;
        if ($pbranch_id <= 0) {
            return $this->packError('pbranch_id', 'must-be-positive-integer');
        }
        return $this->product_repo->getPbranchById($pbranch_id);
    }

    public function getPtagById($ptag_id)
    {
        $ptag_id = (int) $ptag_id;
        if ($ptag_id <= 0) {
            return $this->packError('ptag_id', 'must-be-positive-integer');
        }
        return $this->product_repo->getPtagById($ptag_id);
    }

    public function getPtargetById($ptarget_id)
    {
        $ptarget_id = (int) $ptarget_id;
        if ($ptarget_id <= 0) {
            return $this->packError('ptarget_id', 'must-be-positive-integer');
        }
        return $this->product_repo->getPtargetById($ptarget_id);
    }

    public function getRqtHotTag()
    {
        $res = [];
        $tmp = '';
        $hot_tags = '';
        if ($tmp = $this->cache()->get('hot_rqt_tags')) {
            $hot_tags = json_decode($tmp);
        } else {
            $hot_tags = $this->product_repo->getRqtHotTag();
            $hot_tags = $this->cache()->set('hot_rqt_tags', json_encode($hot_tags), 3600);
        }
        if (is_array($hot_tags)) {
            foreach ($hot_tags as $key => &$tag) {
                $res[$key]['title'] = $tag->title;
                $res[$key]['url'] = route_url('ipar-ui-tag_rqt-index', ['zcode' => $tag->zcode]);
            }
        }
        return $res;
    }

    public function getProductHotTag()
    {
        $res = [];
        $tmp = '' ;
        $hot_tags = '';
        if ($tmp = $this->cache()->get('hot_product_tags')) {
            $hot_tags = json_decode($tmp);
        } else {
            $hot_tags = $this->product_repo->getProductHotTag();
            $hot_tags = $this->cache()->set('hot_product_tags', json_encode($hot_tags), 3600);
        }

        if (is_array($hot_tags)) {
            foreach ($hot_tags as $key => &$tag) {
                $res[$key]['title'] = $tag->title;
                $res[$key]['url'] = route_url('ipar-ui-tag_rqt-index', ['zcode' => $tag->zcode]);
            }
        }
        return $res;
    }
}
