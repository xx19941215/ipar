<?php
namespace Mars\Repo;

use Gap\Repo\RepoBase;

class EntityTagRepo extends RepoBase
{
    protected $tag_table_repo;
    protected $entitiy_tag_table_repo;
    protected $entity_tag_vote;

    public function bootstrap()
    {
        $this->tag_table_repo = new TagTableRepo($this->db);
        $this->entitiy_tag_table_repo = new EntityTagTableRepo($this->db);
        $this->entity_tag_vote = new EntityTagVoteTableRepo($this->db);
    }

    protected function savedTagId($tag_title)
    {
        if ($existed = $this->tag_table_repo->findOne(['title' => $tag_title], ['id'])) {
            return $existed->id;
        }

        $pack = $this->tag_table_repo->create(['title' => $tag_title]);
        if ($pack->isOk()) {
            return $pack->getItem('id');
        }
        return 0;
    }

    protected function savedEntityTagId($eid, $tag_id, $entity_type_id)
    {
        if ($existed = $this->entitiy_tag_table_repo->findOne(['eid' => $eid, 'tag_id' => $tag_id], ['id'])) {
            return $existed->id;
        }

        $pack = $this->entitiy_tag_table_repo->create([
            'eid' => $eid,
            'tag_id' => $tag_id,
            'entity_type_id' => $entity_type_id
        ]);

        if ($pack->isOk()) {
            return $pack->getItem('id');
        }
        return 0;
    }

    public function save($data = [])
    {
        $this->db->beginTransaction();
        $tag_title = prop($data, 'tag_title');
        $tag_title = trim($tag_title);
        $tag_title = preg_replace('!\s+!', ' ', $tag_title);
        $tag_id = $this->savedTagId($tag_title);
        if (!$tag_id) {
            $this->db->rollback();
            return $this->packError('entity_tag', 'create-failed');
        }
        $data['tag_id'] = $tag_id;
        $eid = $data['eid'];
        $data['entity_tag_id'] = $this->savedEntityTagId($eid, $tag_id, $data['entity_type_id']);
        if (!$data['entity_tag_id']) {
            $this->db->rollback();
            return $this->packError('entity_tag', 'create-failed');
        }
        $data['vote_uid'] = $data['uid'];
        $pack = $this->entity_tag_vote->create($data);
        if (!$pack->isOk()) {
            $this->db->rollback();
            return $pack;
        }
        $this->db->commit();
        return $this->packOk();
    }

    public function saveTagMultiple($data = [])
    {
        $titles = $data['titles'];
        $titles = trim($titles);
        $titles = preg_replace('!\s+!', ' ', $titles);
        $titles = str_replace("，", ",", $titles);
        $titles = explode(",", $titles);
        $this->db->beginTransaction();
        foreach ($titles as $title) {
            $data['tag_title'] = $title;
            $pack = $this->save($data);
            if (!$pack->isOk()) {
                $this->db->rollback();
                return $pack;
            }
        }
        $this->db->commit();
        return $this->packOk();
    }


    public function search($data = [])
    {
        $tag_title = prop($data, 'query');
        $ssb = $this->db->select()
            ->setDto('tag')
            ->from(['entity_tag', 'e'])
            ->leftJoin(
                ['tag', 't'],
                ['e', 'tag_id'],
                '=',
                ['t', 'id']
            )
            ->where('eid', '=', $data['eid'])
            ->andWhere('entity_type_id', '=', $data['entity_type_id'])
            ->andWhere('title', 'LIKE', "%$tag_title%");
        return $this->dataSet($ssb);
    }

}