<?php
namespace Ipar\Repo;

class ActivityProductRepo extends IparRepoBase
{
    public function getActivityProductSetByEid($aid)
    {
        $ssb = $this->db->select()
            ->setDto('activity_product')
            ->from('activity_product')
            ->where('activity_id', '=', $aid)
            ->andWhere('show_index', '=', 1);
        return $this->dataSet($ssb);
    }

    public function getActivityProductList()
    {
        $url = $this->db->select()
            ->from(['activity_list', 'a'])
            ->leftJoin(
                ['activity_product', 'b'],
                ['a', 'id'],
                '=',
                ['b', 'activity_id']
            )
            ->where(['b','status'], '=', 1)
            ->andWhere(['a', 'status'], '=', 1)
            ->fetchAll();
        return $url;
    }

    public function addActivityProductByEid($aid, $eid)
    {
        $ssb = $this->db->select()
            ->from('activity_product')
            ->where('product_id', '=', $eid)
            ->Andwhere('activity_id', '=', $aid)
            ->fetchAll();
        if ($ssb) {
            return 0;
        }

        $isb = $this->db->insert('activity_product')
            ->value('activity_id', $aid)
            ->value('product_id', $eid);
        return $isb->execute();
    }

    public function deleteActivityProductByEid($aid, $eid)
    {
        $dsb = $this->db->delete()
            ->from('activity_product')
            ->where('product_id', '=', $eid)
            ->Andwhere('activity_id', '=', $aid);
        return $dsb->execute();
    }

    public function getProductSetByAid($activity_id)
    {
        $ssb = $this->db->select()
            ->setDto('activity_product')
            ->from('activity_product')
            ->where('activity_id', '=', $activity_id);

        return $this->dataSet($ssb);
    }

    public function adviceProductToPage($aid, $eid)
    {
        $ssb = $this->db->select()
            ->from('activity_product')
            ->where('activity_id', '=', $aid)
            ->Andwhere('show_index', '=', 1)
            ->fetchAll();
        if (count($ssb) > 2) {
            return 0;
            exit;
        }

        $usb = $this->db->update('activity_product')
            ->where('product_id', '=', $eid)
            ->Andwhere('activity_id', '=', $aid)
            ->set('show_index', 1);
        return $usb->execute();
    }

    public function cancelAdviceProductToPage($aid, $eid)
    {
        $usb = $this->db->update('activity_product')
            ->where('product_id', '=', $eid)
            ->Andwhere('activity_id', '=', $aid)
            ->set('show_index', 0);
        return $usb->execute();
    }

    public function activeProduct($aid, $eid)
    {
        $usb = $this->db->update('activity_product')
            ->set('status', 1)
            ->where('product_id', '=', $eid)
            ->Andwhere('activity_id', '=', $aid);
        return $usb->execute();
    }

    public function deActiveProduct($aid, $eid)
    {
        $usb = $this->db->update('activity_product')
            ->where('activity_id', '=', $aid)
            ->andWhere('product_id', '=', $eid)
            ->set('status', 0);
        return $usb->execute();
    }

    public function getActiveProductEidByAid($aid)
    {
        $ssb = $this->db->select('product_id')
            ->from('activity_product')
            ->where('activity_id', '=', $aid)
            ->andWhere('status', '=', 1);
        $res = $ssb->fetchAll();
        foreach ($res as $key => &$r) {
            $res[$key] = $r->product_id;
        }
        return $res;
    }
}
