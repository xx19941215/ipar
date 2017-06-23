<?php
namespace Mars\Repo;

class CompanyIndustryTagTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'company_industry_tag';
    protected $dto = 'company_industry_tag';
    protected $fields = [
        'gid' => 'int',
        'tag_id' => 'int'
    ];


    public function searchCompanyIndustryTagSet($query = [])  //查找company下industry_tag
    {
        $gid = prop($query, 'gid', '');
        $ssb = $this->db->select()
            ->setDto('tag')
            ->from(['industry_tag', 'i'])
            ->leftJoin(
                ['tag', 't'],
                ['t', 'id'],
                '=',
                ['i', 'tag_id']
            )
            ->leftJoin(
                ['company_industry_tag', 'c'],
                ['c', 'tag_id'],
                '=',
                ['i', 'tag_id']
            )
            ->select(
                ['t', 'id'],
                ['t', 'title'],
                ['t', 'content'],
                ['c', 'gid']
            )
            ->startGroup()
            ->where(['c', 'gid'], '=', $gid, 'int')
            ->endGroup();

        if ($search = prop($query, 'search', '')) {
            $ssb->andWhere('title', 'LIKE', "%$search%");
        }

        $ssb->orderBy('id', 'desc');

        return $this->dataSet($ssb);
    }

    public function selectCompanyIndustryTagSet($query = [])
    {
        $gid = prop($query, 'exclude_gid', '');
        $ssb = $this->db->select()
            ->setDto('tag')
            ->from(['industry_tag', 'i'])
            ->leftJoin(
                ['tag', 't'],
                ['t', 'id'],
                '=',
                ['i', 'tag_id']
            )
            ->select(
                ['t', 'id'],
                ['t', 'title'],
                ['t', 'content']
            )
            ->leftJoin(
                ['company_industry_tag', 'c'],
                ['c', 'tag_id'],
                '=',
                ['i', 'tag_id']
            )
            ->startGroup()
            ->whereRaw("`i`.`tag_id` NOT IN (SELECT tag_id FROM `company_industry_tag` WHERE `gid` = $gid )")
            ->orWhereRaw('`c`.`tag_id` IS NULL')
            ->endGroup()
            ->groupBy(['i', 'tag_id']);

        if ($search = prop($query, 'search', '')) {
            $ssb->andWhere('title', 'LIKE', "%$search%");
        }

        return $this->dataSet($ssb);
    }

    public function existIndustryTagCompanySet($query = [])  //查找industry_tag下的company
    {
        $tag_id = prop($query, 'tag_id', '');
        $ssb = $this->db->select(
            ['t', 'id'],
            ['t', 'title'],
            ['t', 'content'],
            ['c', 'gid'],
            ['g', 'fullname']
        )
            ->setDto('company')
            ->from(['industry_tag', 'i'])
            ->leftJoin(
                ['tag', 't'],
                ['t', 'id'],
                '=',
                ['i', 'tag_id']
            )
            ->leftJoin(
                ['company_industry_tag', 'c'],
                ['c', 'tag_id'],
                '=',
                ['i', 'tag_id']
            )
            ->leftJoin(
                ['group', 'g'],
                ['g', 'gid'],
                '=',
                ['c', 'gid']
            )
            ->startGroup()
            ->where(['c', 'tag_id'], '=', $tag_id, 'int')
            ->endGroup();

        if ($search = prop($query, 'search', '')) {
            $ssb->andWhere('fullname', 'LIKE', "%$search%");
        }

        $ssb->orderBy(['g','gid'], 'desc');

        return $this->dataSet($ssb);
    }

    public function validate($data)
    {
        $tag_id = prop($data, 'tag_id', '');
        $gid = prop($data, 'gid', '');
        if ($existed = $this->findOne(['gid' => $gid, 'tag_id' => $tag_id])) {
            $this->addError('industry_tag', 'already-exists');
            return false;
        }
        return true;
    }
}