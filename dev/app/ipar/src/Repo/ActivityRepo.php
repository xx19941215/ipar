<?php
namespace Ipar\Repo;

class ActivityRepo extends IparRepoBase
{
    public function getActivitySet()
    {
        $ssb = $this->db->select()
            ->setDto('activity')
            ->from('activity_list');
        return $this->dataSet($ssb);
    }

    public function getSingleActivitySet($activity_id)
    {
        $ssb = $this->db->select()
            ->setDto('activity')
            ->from('activity_list')
            ->where('id', '=', $activity_id)
            ->limit(1);
        return $this->dataSet($ssb);
    }

    public function getActivitySetPreview()
    {
        $res = [];
        $aid = $this->db->select('id')
            ->from('activity_list')
            ->where('status', '=', 1)
            ->fetchOne()
            ->id;
        $res['data'] = $this->db->select()
            ->setDto('activity')
            ->from('activity_list')
            ->where('status', '=', 1)
            ->fetchOne();
        $res['data_detail'] = $this->db->select()
            ->setDto('activity')
            ->from('activity_product')
            ->where('activity_id', '=', $aid)
            ->Andwhere('show_index', '=', 1)
            ->fetchAll();
        return $res;
    }

    public function getActivityIndexImg()
    {
        $url = $this->db->select('img_index')
            ->from('activity_list')
            ->where('status', '=', 1)
            ->fetchOne()
            ->img_index;
        return $url;
    }

    public function getActivityProductList()
    {
        $url = $this->db->select()
            ->from(['activity_list', 'a'])
            ->where(['a', 'status'], '=', 1)
            ->leftJoin(
                ['activity_product', 'b'],
                ['a', 'id'],
                '=',
                ['b', 'activity_id']
            )->fetchAll();
        return $url;
    }

    public function getActivityDate()
    {
        return $this->db->select('created', 'expired')
            ->from('activity_list')
            ->where('status', '=', 1)
            ->fetchOne();
    }

    public function addSingleData($item)
    {
        $isb = $this->db->insert('activity_list')
            ->value('title', $item['title'])
            ->value('description', $item['description'])
            ->value('rule', $item['rule'])
            ->value('status', 0)
            ->value('created', $item['createtime'])
            ->value('expired', $item['endtime']);

        return $isb->execute();
    }

    public function updateImgField($id, $type, $url, $db)
    {
        $usb = $this->db->update($db)
            ->where('id', '=', $id)
            ->set($type, $url);
        return $usb->execute();
    }

    public function updateSingleData($item)
    {
        $usb = $this->db->update('activity_list')
            ->where('id', '=', $item['id'])
            ->set('title', $item['title'])
            ->set('description', $item['description'])
            ->set('rule', $item['rule'])
            ->set('created', $item['createtime'])
            ->set('expired', $item['endtime']);
        return $usb->execute();
    }

    public function adviceToIndexPage($id)
    {
        $res = $this->db->update('activity_list')
            ->set('status', 0)
            ->execute();
        $usb = $this->db->update('activity_list')
            ->where('id', '=', $id)
            ->set('status', 1);
        return $usb->execute();
    }

    public function cancelAllAdviceToIndexPage()
    {
        $res = $this->db->update('activity_list')
            ->set('status', 0)
            ->execute();
    }

    public function deleteActivity($id)
    {
        $dsb = $this->db->delete()
            ->from('activity_list')
            ->where('id', '=', $id);
        return $dsb->execute();
    }

    public function getPopImproving()
    {
        $product_eids = [2511,2512,2513,2514,2515,1344,2531,
            2532,2533,2541,2542,2544];

        $ssb = $this->db->select(
                ['p', 'id'],
                ['im', 'uid'],
                ['p', 'ptype_id'],
                ['im', 'type_id', 'dst_type_id'],
                ['im', 'content'],
                ['im', 'title'],
                ['im', 'eid', 'dst_eid'],
                ['src', 'type_id', 'src_type_id'],
                ['src', 'eid', 'src_eid'],
                ['src', 'title', 'src_title'],
                ['src', 'zcode', 'src_zcode'],
                ['im', 'created']
            )
            ->setDto('pop_improving')
            ->from(['property', 'p'])
            ->leftJoin(
                ['entity', 'src'],
                ['p', 'product_eid'],
                '=',
                ['src', 'eid']
            )
            ->leftJoin(
                ['entity', 'im'],
                ['im', 'eid'],
                '=',
                ['p', 'dst_eid']
            )
            ->leftJoin(
                ['entity_analysis', 'an'],
                ['an', 'eid'],
                '=',
                ['im', 'eid']
            )
            ->where(['p', 'product_eid'], 'IN', $product_eids)
            ->andWhere(['p', 'ptype_id'], '=', 8, 'int')
            ->orderBy(['an', 'like_count'], 'DESC')
            ->orderBy(['an', 'comment_count'], 'DESC')
            ->limit(5);
        return $this->dataSet($ssb);
    }

    public function getIsLike() {
        $uid = current_uid();

        $ssb = $this->db->select('eid')
            ->from('entity_like')
            ->where('uid','=', $uid,'int');
        return $this->dataSet($ssb)->setCountPerPage(0);
    }

    public function getLikeCount()
    {
        $tmp = [];
        $res = $this->db->query(
            "select  count(1) as like_count,eid
            from entity_like where eid
            in (2511,2512,2513,2514,2515,1344,2531,2532,2533,2541,2542,2544)
            group by eid")
            ->fetchAll();
        foreach ($res as &$value) {
            $tmp[$value->eid] = $value->like_count;
        }
        return $tmp;
    }
}

