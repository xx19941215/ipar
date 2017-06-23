<?php

namespace Mars\Repo;

class CompanyRepo extends \Gap\Repo\RepoBase
{
    protected $company_table_repo;
    protected $group_table_repo;

    protected function prepareBd()
    {
        return $this->db->select(
            ['a', 'gid'],
            ['a', 'type_id'],
            ['a', 'zcode'],
            ['a', 'name'],
            ['a', 'fullname'],
            ['a', 'content'],
            ['a', 'status'],
            ['a', 'logo']
        )
            ->from(['group', 'a']);
    }

    public function bootstrap()
    {
        $this->company_table_repo = new CompanyTableRepo($this->db);
        $this->group_table_repo = new GroupTableRepo($this->db);
    }

    public function search($query = [])
    {
        $sort = prop($query, 'sort');
        $ssb = $this->db->select()
            ->setDto('company')
            ->from(['company', 'c'])
            ->leftJoin(
                ['group', 'g'],
                ['g', 'gid'],
                '=',
                ['c', 'gid']
            );

        if ($type_id = prop($query, 'type_id', '')) {
            $ssb->andWhere('type_id', '=', $type_id, 'int');
        }

        if ($is_activated = prop($query, 'is_activated', '')) {
            $ssb->andWhere('status', '=', '1', 'int');
        }

        $ssb->orderBy(
            $sort ? ['g', 'changed'] : ['g', 'rank'],
            'desc'
        );
        $ssb->orderBy(['g', 'gid'], 'desc');

        return $this->dataSet($ssb);
    }

    public function findOne($query)
    {
        $ssb = $this->db->select()
            ->setDto('company')
            ->from(['company', 'c'])
            ->leftJoin(
                ['group', 'g'],
                ['g', 'gid'],
                '=',
                ['c', 'gid']
            );
        $hit = 0;
        if ($gid = prop($query, 'gid', 0)) {
            $ssb->andWhere(['c', 'gid'], '=', $gid, 'int');
            $hit++;
        }

//        zocde
        if ($zcode = prop($query, 'zcode', 0)) {
            $group = $this->group_table_repo->findOne(['zcode' => $zcode]);
            if (!$group) {
                return null;
            }
            $gid = $group->gid;
            $ssb->andWhere(['c', 'gid'], '=', $gid, 'int');
            $hit++;
        }

        if ($hit <= 0) {
            return null;
        }

        return $ssb->fetchOne();
    }

    public function create($data)
    {
        $this->db->beginTransaction();
        $pack_group = $this->group_table_repo->create($data);

        if (!$pack_group->isOk()) {
            $this->db->rollback();
            return $pack_group;
        }

        $gid = $pack_group->getItem('id');
        $data['gid'] = $gid;
        $pack_company = $this->company_table_repo->create($data);

        if (!$pack_company->isOk()) {
            $this->db->rollback();
            return $pack_company;
        }

        $this->db->commit();
        return $this->packItem('id', $gid);
    }

    public function update($query, $data)
    {
        $this->db->beginTransaction();
        $pack_group = $this->group_table_repo->update(['gid' => $data['gid']], $data);
        $pack_company = $this->company_table_repo->update(['gid' => $data['gid']], $data);
        if (!$pack_group->isOk()) {
            $this->db->rollback();
            return $pack_group;
        }
        if (!$pack_company->isOk()) {
            $this->db->rollback();
            return $pack_company;
        }
        if ($pack_group->isOk() && $pack_company->isOk()) {
            $this->db->commit();
            return $pack_company;
        }
    }

    public function delete($gid)
    {
        $this->db->beginTransaction();
        $gid = (int)$gid;
        $pack_company = $this->company_table_repo->delete(['gid' => $gid]);
        if (!$pack_company->isOk()) {
            $this->db->rollback();
            return $pack_company;
        }
        // $this->company_table_repo->delete 执行失败, 就没有必要再往下执行

        $pack_group = $this->group_table_repo->delete(['gid' => $gid]);
        if (!$pack_group->isOk()) {
            $this->db->rollback();
            return $pack_group;
        }

        $this->db->commit();
        return $this->packOk();
    }

    public function updateField($query, $field, $value)
    {
        $group_repo = gap_repo_manager()->make('group');
        return $group_repo->updateField($query, $field, $value);
    }

    public function schCompanyBd($opts = [])
    {
        $query = prop($opts, 'query', '');

        $builder = $this->prepareBd();

        if ($query) {
            $builder->startGroup()
                ->where(['a', 'title'], 'LIKE', "%{$query}%")
                ->orWhere(['a', 'content'], 'LIKE', "%{$query}%")
                ->endGroup();
        }

        $order = prop($opts, 'order', 'default');

        if ($order == 'default') {
            $builder->orderBy(['a', 'gid'], 'DESC');

        } else if ($order == 'created') {
            $builder->orderBy(['a', 'gid'], 'DESC');

        } else {
            var_dump($order);
            _debug('unexpected entity order');
        }

        return $builder;
    }

    public function schCompanySet($opts = [])
    {
        return $this->dataSet(
            $this->schCompanyBd($opts)
        );
    }
}
