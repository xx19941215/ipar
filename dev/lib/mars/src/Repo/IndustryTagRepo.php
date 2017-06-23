<?php
namespace Mars\Repo;

class IndustryTagRepo extends \Gap\Repo\RepoBase
{
    protected $tag_table_repo;
    protected $industry_tag_table_repo;


    public function getTagTableRepo()
    {
        return new TagTableRepo($this->db);
    }

    public function getIndustryTagTableRepo()
    {
        return new IndustryTagTableRepo($this->db);
    }

    public function bootstrap()
    {
        $this->tag_table_repo = $this->getTagTableRepo();
        $this->industry_tag_table_repo = $this->getIndustryTagTableRepo();
    }

    public function search($query = [])
    {
        $ssb = $this->db->select()
            ->setDto('industry_tag')
            ->from(['industry_tag', 'i'])
            ->leftJoin(
                ['tag', 't'],
                ['t', 'id'],
                '=',
                ['i', 'tag_id']
            );

        if ($search = prop($query, 'search', '')) {
            $ssb->where('title', 'LIKE', "%$search%");
        }

        $ssb->orderBy('changed', 'desc');
        return $this->dataSet($ssb);
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

        if ($this->industry_tag_table_repo->findOne(['tag_id' => $data['tag_id']])) {
            return $this->packItem('id', $data['tag_id']);
        }

        $pack_industry_tag = $this->industry_tag_table_repo->create($data);

        if (!$pack_industry_tag->isOk()) {
            $this->db->rollback();
            return $pack_industry_tag;
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


    public function delete($query)
    {
        return $this->industry_tag_table_repo->delete($query);
    }

    public function findOne($query)
    {
        $ssb = $this->db->select()
            ->setDto('industry_tag')
            ->from(['industry_tag', 'i'])
            ->leftJoin(
                ['tag', 't'],
                ['t', 'id'],
                '=',
                ['i', 'tag_id']
            );
        $hit = 0;

        if ($tag_id = prop($query, 'tag_id', 0)) {
            $ssb->andWhere(['i', 'tag_id'], '=', $tag_id, 'int');
            $hit++;
        }

        if ($hit <= 0) {
            return null;
        }

        return $ssb->fetchOne();
    }
}