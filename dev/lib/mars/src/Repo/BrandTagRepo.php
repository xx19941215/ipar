<?php
namespace Mars\Repo;

class BrandTagRepo extends \Gap\Repo\RepoBase
{
    protected $tag_table_repo;
    protected $brand_tag_table_repo;

    public function bootstrap()
    {
        $this->tag_table_repo = new TagTableRepo($this->db);
        $this->brand_tag_table_repo = new BrandTagTableRepo($this->db);
    }

    public function search($query = [])
    {
        $ssb = $this->db->select()
            ->setDto('tag')
            ->from(['brand_tag', 'i'])
            ->leftJoin(
                ['tag', 't'],
                ['t', 'id'],
                '=',
                ['i', 'tag_id']
            );
        if ($search = prop($query, 'search', '')) {
            $ssb->where(['t','title'], 'LIKE', "%$search%");
        }

        $ssb->orderBy(['t','changed'], 'desc');
        return $this->dataSet($ssb);
    }

    public function findOne($query)
    {
        $ssb = $this->db->select()
            ->setDto('brand_tag')
            ->from(['brand_tag', 'b'])
            ->leftJoin(
                ['tag', 't'],
                ['t', 'id'],
                '=',
                ['b', 'tag_id']
            );
        $hit = 0;

        if ($tag_id = prop($query, 'tag_id', 0)) {
            $ssb->andWhere(['b', 'tag_id'], '=', $tag_id, 'int');
            $hit++;
        }

        if ($hit <= 0) {
            return null;
        }

        return $ssb->fetchOne();
    }

    public function save($data)
    {
        $this->db->beginTransaction();
        $title = trim(prop($data, 'title'));
        $title = preg_replace('!\s+!', ' ', $title);
        $data['title'] = $title;
        $existed = $this->existTagId($data);

        if (!$existed) {
            $this->db->rollback();
            return $this->packError('tag', 'create-failed');
        }

        if ($this->brand_tag_table_repo->findOne(['tag_id' => $data['tag_id']])) {
            return $this->packItem('id', $data['tag_id']);
        }

        $pack_brand_tag = $this->brand_tag_table_repo->create($data);

        if (!$pack_brand_tag->isOk()) {
            $this->db->rollback();
            return $pack_brand_tag;
        }

        $this->db->commit();

        return $this->packItem('id', $data['tag_id']);
    }
    protected function existTagId(&$data)
    {
        if ($existed = $this->tag_table_repo->findOne(['title' => $data['title']])) {
            $data['tag_id'] = $existed->id;
            return true;
        }

        $pack = $this->tag_table_repo->create($data);

        if ($pack->isOk()) {
            $data['tag_id'] = $pack->getItem('id');
            return true;
        }

        return false;
    }
}