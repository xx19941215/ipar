<?php
namespace Ipar\Repo;

trait BrandProductRepoTrait
{
    public function saveBrandProduct($data)
    {
        $this->db->beginTransaction();

        $pack = $this->saveBrand([
            'title' => prop($data, 'title'),
            'locale_id' => prop($data, 'locale_id')
        ]);

        if (!$pack->isOk()) {
            $this->db->rollback();
            return $pack;
        }

        $brand_id = $pack->getItem('brand_id');
        $product_eid = (int) prop($data, 'product_eid', 0);

        $brand_product = $this->findBrandProduct([
            'brand_id' => $brand_id,
            'product_eid' => $product_eid
        ]);

        $brand_product_id = 0;
        if ($brand_product) {
            $brand_product_id = $brand_product->id;
        }

        if (!$brand_product_id) {
            $pack = $this->createBrandProduct([
                'product_eid' => $product_eid,
                'brand_id' => $brand_id
            ]);
            if (!$pack->isOk()) {
                $this->db->rollback();
                return $pack;
            }
            $brand_product_id = $pack->getItem('brand_product_id');
        }

        $pack = $this->voteBrandProduct($brand_product_id);
        if (!$pack->isOk()) {
            $this->db->rollback();
            return $pack;
        }

        $this->db->commit();
        return $this->packItem('brand_product_id', $brand_product_id);
    }

    public function createBrandProduct($data)
    {
        if ($this->db->insert('brand_product')
            ->value('product_eid', (int) prop($data, 'product_eid', 0))
            ->value('brand_id', (int) prop($data, 'brand_id', 0))
            ->value('vote_count', 0)
            ->execute()
        ) {
            return $this->packItem('brand_product_id', $this->db->lastInsertId());
        }

        return $this->packError('brand_product', 'insert-failed');
    }

    public function deleteBrandProduct($query = [])
    {
    }

    public function schBrandProductSsb($query = [])
    {
        $ssb = $this->db->select()
            ->setDto('brand_product')
            ->from('brand_product');

        if ($product_eid = (int) prop($query, 'product_eid', 0)) {
            $ssb->andWhere('product_eid', '=', $product_eid, 'int');
        }
        if ($brand_id = (int) prop($query, 'brand_id', 0)) {
            $ssb->andWhere('brand_id', '=', $brand_id, 'int');
        }
        if ($brand_product_id = (int) prop($query, 'brand_product_id', 0)) {
            $ssb->andWhere('id', '=', $brand_product_id, 'int');
        }

        $ssb->orderBy('vote_count', 'DESC');
        $ssb->orderBy('id', 'DESC');

        return $ssb;
    }

    public function findBrandProduct($query = [])
    {
        $ssb = $this->schBrandProductSsb($query);
        if (!$ssb->getWheres()) {
            return null;
        }
        return $ssb->fetchOne();
    }
}
