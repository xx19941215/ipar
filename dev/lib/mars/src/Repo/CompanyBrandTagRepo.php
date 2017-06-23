<?php
namespace Mars\Repo;

class CompanyBrandTagRepo extends \Gap\Repo\RepoBase
{
    protected $company_brand_tag_table_repo;
    protected $brand_tag_repo;
    protected $tag_table_repo;

    public function bootstrap(){
        $this->company_brand_tag_table_repo = new CompanyBrandTagTableRepo($this->db);
        $this->brand_tag_repo = new BrandTagRepo($this->db);
        $this->tag_table_repo = new TagTableRepo($this->db);
    }

    public function save($data)
    {

        $isExistInBrandTagTable = $this->isExistInBrandTagTable($data);

        if ($isExistInBrandTagTable) {
            return $this->company_brand_tag_table_repo->create($data);
        }

        $this->db->beginTransaction();
        $pack = $this->brand_tag_repo->save($data);

        if (!$pack->isOk()) {
            $this->db->rollback();
            return $pack;
        }

        $this->db->commit();
        $data['tag_id'] = $pack->getItem('id');

        return $this->company_brand_tag_table_repo->create($data);
    }

    public function isExistInBrandTagTable(&$data)
    {

        if (!$tag = $this->tag_table_repo->findOne(['title' => $data['title']])) {
            return false;
        }

        if ($this->brand_tag_repo->findOne(['tag_id' => $tag->id])) {
            $data['tag_id'] = $tag->id;
            return true;
        }

        return false;
    }

    public function getCompanyBrandTagSet($query = [])  //查找company下brand_tag
    {
        $gid = prop($query, 'gid', '');
        $ssb = $this->db->select()
            ->setDto('tag')
            ->from(['brand_tag', 'b'])
            ->leftJoin(
                ['tag', 't'],
                ['t', 'id'],
                '=',
                ['b', 'tag_id']
            )
            ->leftJoin(
                ['company_brand_tag', 'c'],
                ['c', 'tag_id'],
                '=',
                ['b', 'tag_id']
            )
            ->startGroup()
            ->where(['c', 'gid'], '=', $gid, 'int')
            ->endGroup();

        $ssb->orderBy(['t','id'], 'desc');

        return $this->dataSet($ssb);
    }

    public function findOne($query)
    {
        return $this->company_brand_tag_table_repo->findOne($query);
    }

    public function unlink($data)
    {
        return $this->company_brand_tag_table_repo->delete(['id' => $data['id']]);
    }
}