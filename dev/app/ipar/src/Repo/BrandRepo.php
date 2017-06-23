<?php
namespace Ipar\Repo;

class BrandRepo extends \Mars\RepoBase
{
    use BrandMainRepoTrait;

    use BrandProductRepoTrait;
    use BrandProductVoteRepoTrait;

    public function schBrandSsb($query = [], $fields = [])
    {
        $ssb = $this->db->select(['b', '*'])
            ->from(['brand', 'b'])
            ->leftJoin(
                ['brand_main', 'bm'],
                ['bm', 'brand_id'],
                '=',
                ['b', 'id']
            )
            ->setDto('brand');

        if ($fields) {
            $ssb->fields($fields);
        }

        if (null !== ($status = prop($query, 'status', 1))) {
            $ssb->andWhere(['b', 'status'], '=', $status, 'int');
        }

        if ($brand_id = prop($query, 'brand_id', 0)) {
            $ssb->andWhere(['b', 'id'], '=', $brand_id, 'int');
        }

        if ($title = prop($query, 'title', '')) {
            $ssb->andWhere(['bm', 'title'], '=', $title);
        }

        if ($keywords = prop($query, 'keywords')) {
            $ssb->andWhere(['bm', 'title'], 'LIKE', "$keywords%");
        }
        return $ssb;
    }

    public function findBrand($query = [], $fields = [])
    {
        $ssb = $this->schBrandSsb($query, $fields);
        if (!$ssb->getWheres()) {
            return $this->packError('query', 'error-query');
        }
        return $ssb->fetchOne();
    }

    public function saveBrand($data)
    {
        $title = trim(prop($data, 'title', ''));
        $locale = (int) prop($data, 'locale', 0);

        if ($brand = $this->findBrand(['title' => $title, 'locale' => $locale])) {
            return $this->packItem('brand_id', $brand->id);
        }

        $this->db->beginTransaction();

        $create_brand = $this->createBrand();
        if (!$create_brand->isOk()) {
            $this->db->rollback();
            return $create_brand;
        }

        $brand_id = $create_brand->getItem('brand_id');
        $create_brand_main = $this->createBrandMain([
            'title' => $title,
            'locale' => $locale,
            'brand_id' => $brand_id
        ]);
        if (!$create_brand_main->isOk()) {
            $this->db->rollback();
            return $create_brand_main;
        }

        $this->db->commit();
        return $this->packItem('brand_id', $brand_id);
    }

    public function createBrand($data = [])
    {
        if ($this->db->insert('brand')
            ->value('logo', trim(prop($data, 'logo', '')))
            ->value('status', (int) prop($data, 'status', 1))
            ->execute()
        ) {
            return $this->packItem('brand_id', $this->db->lastInsertId());
        }

        return $this->packError('brand', 'insert-failed');
    }

    public function changeStatus($brand_id, $status)
    {
    }

    public function deleteBrand($query)
    {
        $dsb = $this->db->delete('b', 'bm', 'bp', 'bpv')
            ->from(['brand', 'b'])
            ->leftJoin(
                ['brand_main', 'bm'],
                ['bm', 'brand_id'],
                '=',
                ['b', 'id']
            )
            ->leftJoin(
                ['brand_product', 'bp'],
                ['bp', 'brand_id'],
                '=',
                ['b', 'id']
            )
            ->leftJoin(
                ['brand_product_vote', 'bpv'],
                ['bpv', 'brand_product_id'],
                '=',
                ['bp', 'id']
            );

        if ($brand_id = (int) prop($query, 'brand_id')) {
            $dsb->where(['b', 'id'], '=', $brand_id);
        }

        if ($title = prop($query, 'title')) {
            $brand = $this->findBrand([
                'title' => $title
            ]);
            if ($brand) {
                $dsb->andWhere(['b', 'id'], '=', $brand->id);
            }
        }

        if (!$dsb->getWheres()) {
            return $this->packError('query', 'error-query');
        }

        if ($dsb->execute()) {
            return $this->packItem('brand_id', $brand_id);
        }

        return $this->packError('brand', 'delete-failed');
    }
}
