<?php
namespace Mars\Repo;

class CompanyProductRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'company_product';
    protected $dto = 'company_product';
    protected $fields = [
        'id' => 'int',
        'gid' => 'int',
        'eid' => 'int'
    ];

    public function searchExistProductSet($query = [])
    {
        $gid = prop($query, 'gid', '');
        $ssb = $this->db->select()
            ->setDto('entity')
            ->from(['entity', 'e'])
            ->leftJoin(
                ['company_product', 'c'],
                ['c', 'eid'],
                '=',
                ['e', 'eid']
            )
            ->where(['e', 'type_id'], '=', get_type_id('product'), 'int')
            ->andWhere(['c', 'gid'], '=', $gid, 'int');

        if ($search = prop($query, 'search', '')) {
            $ssb->andWhere(['e', 'title'], 'LIKE', "%$search%");
        }
        $ssb->orderBy('added', 'desc');

        return $this->dataSet($ssb);
    }

    public function selectProductSet($query = [])
    {
        $gid = prop($query, 'exclude_gid', '');
        $ssb = $this->db->select(
            ['e', 'title'],
            ['e', 'eid'],
            ['e', 'type_id'],
            ['e', 'content'],
            ['e', 'created'],
            ['e', 'changed'],
            ['c', 'gid']
        )
            ->setDto('product')
            ->from(['entity', 'e'])
            ->leftJoin(
                ['company_product', 'c'],
                ['c', 'eid'],
                '=',
                ['e', 'eid']
            )
            ->startGroup()
            ->whereRaw("`c`.`eid` NOT IN (SELECT eid FROM `company_product` WHERE `gid` = $gid)")
            ->orWhereRaw('`c`.`gid` IS NULL')
            ->endGroup()
            ->groupBy(['e', 'eid'])
            ->andWhere(['e', 'type_id'], '=', get_type_id('product'), 'int');

        if ($search = prop($query, 'search', '')) {
            $ssb->andWhere(['e', 'title'], 'LIKE', "%$search%");
        }

        $ssb->orderBy('changed', 'desc');
        return $this->dataSet($ssb);
    }

    public function selectCompanySet($query = [])
    {
        $eid = prop($query, 'eid', '');
        $ssb = $this->db->select(
            ['g', 'gid'],
            ['g', 'type_id'],
            ['g', 'fullname'],
            ['c', 'eid']
        )
            ->setDto('group')
            ->from(['group', 'g'])
            ->leftJoin(
                ['company_product', 'c'],
                ['c', 'gid'],
                '=',
                ['g', 'gid']
            )
            ->startGroup()
            ->whereRaw("`c`.`gid` NOT IN (SELECT gid FROM `company_product` WHERE `eid` = $eid)")
            ->orWhereRaw('`c`.`gid` IS NULL')
            ->endGroup()
            ->groupBy(['g', 'gid'])
            ->andWhere(['g', 'type_id'], '=', get_type_id('company'), 'int');

        if ($search = prop($query, 'search', '')) {
            $ssb->andWhere(['g', 'fullname'], 'LIKE', "%$search%");
        }

        $ssb->orderBy('changed', 'desc');
        return $this->dataSet($ssb);
    }


    public function searchExistCompanySet($query = [])
    {
        $eid = prop($query, 'eid', '');
        $ssb = $this->db->select(
            ['g', 'gid'],
            ['g', 'type_id'],
            ['g', 'fullname'],
            ['g', 'created'],
            ['g', 'zcode']
        )
            ->setDto('group')
            ->from(['group', 'g'])
            ->leftJoin(
                ['company_product', 'c'],
                ['c', 'gid'],
                '=',
                ['g', 'gid']
            )
            ->where(['c', 'eid'], '=', "$eid");

        if ($search = prop($query, 'search', '')) {
            $ssb->andWhere(['g', 'fullname'], 'LIKE', "%$search%");
        }

        $ssb->orderBy('added', 'desc');
        return $this->dataSet($ssb);
    }

    public function validate($data)
    {
        $eid = prop($data, 'eid', '');
        $gid = prop($data, 'gid', '');
        if ($existed = $this->findOne(['eid' => $eid, 'gid' => $gid])) {
            $this->addError('product', 'already-exists');
            return false;
        }
        return true;
    }
}
