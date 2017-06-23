<?php
namespace Mars\Repo;

class StoryRepo extends \Gap\Repo\RepoBase
{
    public function create($data)
    {
        $action = prop($data, 'action', 0);
        $dst_type_key = prop($data, 'dst_type_key', 0);
        $dst_eid = prop($data, 'dst_eid', 0);
        $submit_id = prop($data, 'submit_id', 0);
        $src_eid = prop($data, 'src_eid', 0);
        $uid = prop($data, 'uid', 0);

        if (!$this->db->insert('story')
            ->value('action', get_action_id($action), 'int')
            ->value('dst_type_id', get_type_id($dst_type_key), 'int')
            ->value('dst_eid', $dst_eid)
            ->value('e_submit_id', (int) $submit_id, 'int')
            ->value('src_eid', $src_eid, 'int')
            ->value('uid', $uid)
            ->execute()
        ) {
            return $this->packError('story', 'create-failed');
        }

        return $this->packItem('id', $this->db->lastInsertId());
    }

    public function search($query = [])
    {
        $eid = prop($query, 'eid', 0);
        $type_key = prop($query, 'type_key', 0);
        $ssb = $this->prepareSsb();
        $status = prop($query, 'status');

        if ($status !== null) {
            $status = (int) $status;
            $ssb->andWhere(['dst', 'status'], '=', 1, 'int');
        }
        if ($eid) {
            $ssb->startGroup('AND')
                ->where(['src', 'eid'], '=', $eid, 'int')
                ->orWhere(['dst', 'eid'], '=', $eid, 'int')
                ->endGroup();
        } else if ($type_key) {
            $type_id = get_type_id($type_key);
            $ssb->startGroup('AND')
                ->where(['dst', 'type_id'], '=', $type_id, 'int')
                ->endGroup();
        }
        if ($uid = prop($query, 'uid', 0)) {
            $ssb->andWhere(['s', 'uid'], '=', $uid, 'int');
        }
        if ($tag_id = prop($query, 'tag_id', 0)) {
            $ssb->leftJoin(
                ['tag_dst', 'dst_td'],
                ['dst_td', 'dst_id'],
                '=',
                ['dst', 'eid']
            );
            $ssb->andWhere(['dst_td', 'tag_id'], '=', $tag_id, 'int');
        }

        $ssb->orderBy(['s','id'], 'desc');

        return $this->dataSet($ssb);
    }

    public function getStoryById($id)
    {
        $id = (int) $id;
        return $this->db->select(
                ['s', 'id'],
                ['s', 'uid'],
                ['s', 'action_id'],
                ['s', 'dst_type_id'],
                ['mt', 'data'],
                ['dst', 'eid', 'dst_eid'],
                ['src', 'type_id', 'src_type_id'],
                ['src', 'eid', 'src_eid'],
                ['src', 'title', 'src_title'],
                ['s', 'created']
            )
            ->from(['story', 's'])
            ->leftJoin(
                ['e_submit', 'mt'],
                ['mt', 'id'],
                '=',
                ['s', 'e_submit_id']
            )
            ->leftJoin(
                ['entity', 'src'],
                ['src', 'eid'],
                '=',
                ['s', 'src_eid']
            )
            ->leftJoin(
                ['entity', 'dst'],
                ['dst', 'eid'],
                '=',
                ['mt', 'eid']
            )
            ->setDto('story')
            ->where(['s', 'id'], '=', $id, 'int')
            ->fetchOne();
    }

    protected function prepareSsb()
    {
        return $this->db->select(
                ['s', 'id'],
                ['s', 'uid'],
                ['s', 'action_id'],
                ['s', 'dst_type_id'],
                ['mt', 'data'],
                ['dst', 'eid', 'dst_eid'],
                ['dst', 'zcode', 'dst_zcode'],
                ['src', 'type_id', 'src_type_id'],
                ['src', 'eid', 'src_eid'],
                ['src', 'zcode', 'src_zcode'],
                ['src', 'title', 'src_title'],
                ['s', 'created'],
                ['mt', 'imgs']
            )
            ->from(['story', 's'])
            ->leftJoin(
                ['e_submit', 'mt'],
                ['mt', 'id'],
                '=',
                ['s', 'e_submit_id']
            )
            ->leftJoin(
                ['entity', 'src'],
                ['src', 'eid'],
                '=',
                ['s', 'src_eid']
            )
            ->leftJoin(
                ['entity', 'dst'],
                ['dst', 'eid'],
                '=',
                ['mt', 'eid']
            )
            ->setDto('story');
    }

}
