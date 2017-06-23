<?php
namespace Ipar\Repo;


class IdeaRepo extends \Mars\Repo\EntityRepo
{
    public function create($uid, $rqt_eid, $content)
    {
        $this->db->beginTransaction();

        $entity_repo = $this->repo('entity');
        $entity_created = $entity_repo->create($uid, 'idea', '', $content);

        $eid = $entity_repo->lastInsertId();
        $inserted = $this->db->insert('idea')
            ->value('eid', $eid)
            ->value('rqt_eid', $rqt_eid)
            //->value('content', $content)
            ->execute();

        if (!$inserted) {
            $this->db->rollback();
            return $this->packError('idea', 'insert-failed');
        }

        $story_created = $this->repo('story')->create($uid, 'create', $eid, $rqt_eid);

        if (!$story_created) {
            $this->db->rollback();
            return $this->packError('story', 'create-failed');
        }

        $this->db->commit();
        return $this->packOk();
    }

    public function getIdeaEidsByRqtEid($rqt_eid, $limit = 10, $offset = 0, $status = 1)
    {
        if($status !== null) {
            $objs = $this->db->select(
                    ['s', 'dst_eid']
                )
                ->from(['solution', 's'])
                ->leftJoin(
                    ['entity', 'e'],
                    ['e', 'eid'],
                    '=',
                    ['s', 'dst_eid']
                )
  //              ->setFetchAssoc()
                ->where(['s', 'rqt_eid'], '=', $rqt_eid, 'int')
                ->andWhere(['s', 'type'], '=', 'idea', 'int')
                ->andWhere(['e', 'status'], '=', $status, 'int')
                ->limit($limit)
                ->offset($offset)
                ->orderBy('dst_eid', 'DESC')
                ->fetchAll();
        } else {
            $objs = $this->db->select('dst_eid')
                ->from('solution')
                ->where('type', '=', 'idea')
                ->andWhere('rqt_eid', '=', $rqt_eid)
                ->limit($limit)
                ->offset($offset)
                ->orderBy('dst_eid', 'DESC')
                ->fetchAll();
        }

        $eids = [];
        if ($objs) {
            foreach ($objs as $obj) {
                $eids[] = $obj->dst_eid;
            }
        }

        return $eids;
    }

    public function getIdeaCountByRqtEid($rqt_eid, $status = 1)
    {
        if($status !== null) {
            $count = $this->db->select(
                    ['s', 'dst_eid']
                )
                ->from(['solution', 's'])
                ->leftJoin(
                    ['entity', 'e'],
                    ['e', 'eid'],
                    '=',
                    ['s', 'dst_eid']
                )
  //              ->setFetchAssoc()
                ->where(['s', 'rqt_eid'], '=', $rqt_eid, 'int')
                ->andWhere(['s', 'type'], '=', 'idea', 'int')
                ->andWhere(['e', 'status'], '=', $status, 'int')
                ->count();
        } else {
            $count = $this->db->select('dst_eid')
                ->from('solution')
                ->where('type', '=', 'idea')
                ->andWhere('rqt_eid', '=', $rqt_eid)
                ->count();
        }

        return $count;
    }

    public function updateIdea($eid, $content = '')
    {

        $this->db->beginTransaction();

        $this->updateEntity($eid, ['content' => $content]);

        $this->createStory('update', 'idea', $eid, $this->lastSubmitId());

        $this->db->commit();
        return $this->packOk();
        /*
        $opts = $this->updateEntity($eid, 'idea', func_get_args());
        $eid = $opts['eid'];

        if (!$this->db->update('idea')
            ->where('eid', '=', $eid, 'int')
            ->set('content', $content)
            ->execute()
        ) {
            return $this->rollbackEntity($opts, 'idea', 'update-failed');
        }

        return $this->commitEntity($opts);
         */
    }

    public function deleteIdea($eid)
    {
        $this->db->beginTransaction();

        if (!$this->db->delete()
            ->from('entity')
            ->where('eid', '=', $eid, 'int')
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('entity', 'delete-failed');
        }

        if (!$this->db->delete()
            ->from('solution')
            ->where('dst_eid', '=', $eid, 'int')
            ->execute()
        ) {
            $this->db->rollback();
            return $this->packError('solution', 'delete-failed');
        }

        $this->db->commit();
        return $this->packOk();
    }

    public function getIdeaByZcode($zcode)
    {
        return $this->getEntityByZcode($zcode);
    }

    public function getRqtByIdeaEid($idea_eid)
    {
        $solution = $this->db->select('rqt_eid')
            ->from('solution')
            ->where('dst_eid', '=', $idea_eid, 'int')
            ->fetchOne();

        if ($solution) {
            return $this->getEntityByEid($solution->rqt_eid);
        } else {
            return null;
        }
    }

    public function schIdeaSet($opts = [])
    {
        return $this->dataSet(
            $this->schEntitySsb($opts)
        );
    }

}
