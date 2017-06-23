<?php
namespace Mars\Repo;

class BrandTagProductTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'brand_tag_product';
    protected $dto = 'brand_tag_product';
    protected $fields = [
        'id' => 'int',
        'brand_tag_id' => 'int',
        'eid' => 'int'
    ];

    public function getBrandTagProductSet($query)
    {
        $brand_tag_id = prop($query, 'brand_tag_id', '');
        $ssb = $this->db->select()
            ->setDto('entity')
            ->from(['entity', 'e'])
            ->leftJoin(
                ['brand_tag_product', 'b'],
                ['b', 'eid'],
                '=',
                ['e', 'eid']
            )
            ->where(['e', 'type_id'], '=', get_type_id('product'), 'int')
            ->andWhere(['b', 'brand_tag_id'], '=', $brand_tag_id, 'int');

        $ssb->orderBy(['b', 'created'], 'desc');
        return $this->dataSet($ssb);
    }

    public function selectLinkProductSet($query = [])
    {
        $brand_tag_id = prop($query, 'exclude_tag_id', '');
        $ssb = $this->db->select(
            ['e', 'title'],
            ['e', 'eid'],
            ['e', 'type_id'],
            ['e', 'content'],
            ['e', 'created'],
            ['e', 'changed']
        )
            ->setDto('product')
            ->from(['entity', 'e'])
            ->leftJoin(
                ['brand_tag_product', 'b'],
                ['b', 'eid'],
                '=',
                ['e', 'eid']
            )
            ->startGroup()
            ->whereRaw("`b`.`eid` NOT IN (SELECT eid FROM `brand_tag_product` WHERE `brand_tag_id` = $brand_tag_id)")
            ->orWhereRaw('`b`.`brand_tag_id` IS NULL')
            ->endGroup()
            ->groupBy(['e', 'eid'])
            ->andWhere(['e', 'type_id'], '=', get_type_id('product'), 'int');

        if ($search = prop($query, 'search', '')) {
            $ssb->andWhere(['e', 'title'], 'LIKE', "%$search%");
        }

        $ssb->orderBy(['e', 'changed'], 'desc');
        return $this->dataSet($ssb);
    }

    public function productGetBrandTag($query)
    {
        $brand_tag_product = $this->findOne($query);

        if (!$brand_tag_product) {
            return null;
        }

        $company_brand_tag_service = gap_service_manager()->make('company_brand_tag');
        $brand_tag_service = gap_service_manager()->make('brand_tag');

        $company_brand_tag = $company_brand_tag_service->findOne([
            'id' => $brand_tag_product->brand_tag_id
        ]);

        if (!$company_brand_tag) {
            return null;
        }

        $brand_tag = $brand_tag_service->findOne([
            'tag_id' => $company_brand_tag->tag_id
        ]);

        return $brand_tag;
    }

    public function validate($data)
    {
        $brand_tag_id = prop($data, 'brand_tag_id', '');
        $eid = prop($data, 'eid', '');

        if ($existed = $this->findOne(['eid' => $eid, 'brand_tag_id' => $brand_tag_id])) {
            $this->addError('product', 'already-exists');
            return false;
        }

        return true;
    }
}
