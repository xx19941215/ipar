<?php
namespace Ipar\Repo;

trait BrandMainRepoTrait
{
    public function createBrandMain($data)
    {
        if ($this->db->insert('brand_main')
            ->value('brand_id', prop($data, 'brand_id'), 'int')
            ->value('locale_id', prop($data, 'locale_id'), 'int')
            ->value('zcode', prop($data, 'zcode', $this->generateZcode()))
            ->value('title', prop($data, 'title', ''))
            ->value('content', prop($data, 'content', ''))
            ->execute()
        ) {
            return $this->packOk();
        }

        return $this->packError('brand_main', 'insert-failed');
    }

    public function updateBrandMain($data)
    {
    }

    public function deleteBrandMain($query)
    {
    }

    public function schBrandMainSsb($query = [])
    {
        $ssb = $this->db->select()
            ->from('brand_main')
            ->setDto('brand_main');

        if ($brand_id = (int) prop($query, 'brand_id')) {
            $ssb->andWhere('brand_id', '=', $brand_id, 'int');
        }

        if ($locale_id = (int) prop($query, 'locale_id')) {
            $ssb->andWhere('locale_id', '=', $locale_id, 'int');
        }

        if ($title = prop($query, 'title')) {
            $ssb->andWhere('title', '=', $title);
        }

        if ($zcode = prop($query, 'zcode')) {
            $ssb->andWhere('zcode', '=', $zcode);
        }

        return $ssb;
    }

    public function findBrandMain($query = [])
    {
        $ssb = $this->schBrandMainSsb($query);
        if (!$ssb->getWheres()) {
            return $this->packError('query', 'error-query');
        }
        return $ssb->fetchOne();
    }

    public function fetchBrandMain($brand_id, $locale_id = 0)
    {
        $ssb = $this->schBrandMainSsb([
            'brand_id' => $brand_id,
            'locale_id' => $locale_id
        ]);
        if ($brand_main = $ssb->fetchOne()) {
            return $brand_main;
        }

        $ssb = $this->schBrandMainSsb([
            'brand_id' => $brand_id
        ]);
        if ($brand_main = $ssb->fetchOne()) {
            return $brand_main;
        }

        return null;
    }
}
