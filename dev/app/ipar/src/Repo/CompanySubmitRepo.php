<?php
namespace Ipar\Repo;

class CompanySubmitRepo extends IparRepoBase
{
    public function getActivitySetAll()
    {
         $ssb = $this->db->select()
               ->setDto('company_submit')
               ->from('company_submit')
               ->orderBy('createtime', 'desc');
        return $this->DataSet($ssb);
    }

    public function getCount($type)
    {
        $count = $type ?
        $this->db->select()
                ->setDto('company_submit')
                ->from('company_submit')
                ->where('status', '=', $type)
                ->count()
        :
        $this->db->select()
                    ->setDto('company_submit')
                    ->from('company_submit')
                    ->count();
        return $count;
    }

    public function getOneActivitySet($id)
    {
        return $this->db->select()
               ->setDto('company_submit')
               ->from('company_submit')
               ->where('id', '=', $id)
               ->fetchOne();
    }

    public function getMarkSetById($id)
    {
        return $this->db->select()
                ->setDto('company_submit')
               ->from('company_submit_review')
               ->where('uid', '=', $id)
               ->fetchAll();
    }

    public function addOneMark($mark_content, $admin, $uid)
    {
        if (!$uid) {
            return;
        }

        $isb = $this->db->insert('company_submit_review');
        $isb
            ->value('uid', $uid)
            ->value('admin', $admin)
            ->value('content', $mark_content)
            ->value('marktime', date('Y-m-d H:i:s', time(null)));

        $inserted = $isb->execute();

        $this->db
            ->update('company_submit')
            ->where('id', '=', $uid)
            ->set('lasted_answered_time', date('Y-m-d H:i:s'))
            ->execute();
    }

    public function addSingleCompanySubmitMsg($res)
    {
        return $this->db->insert('company_submit')
            ->value('username', $res['username'])
            ->value('company', $res['company'])
            ->value('job', $res['job'])
            ->value('email', $res['email'])
            ->value('content', $res['content'])
            ->value('phone', $res['phone'])
            ->value('lasted_answered', '')
            ->value('lasted_answered_time', date('Y-m-d H:i:s', strtotime(0)))
            ->value('createtime', date('Y-m-d H:i:s', time(null)))
            ->value('status', 'unhandle')
            ->execute();
    }

    public function changeStatus($uid, $status, $admin)
    {
        if (!$uid) {
            return;
        }

        $this->db
            ->update('company_submit')
            ->where('id', '=', $uid)
            ->set('status', $status)
            ->set('lasted_answered', $admin)
            ->execute();
    }

    public function search($search, $status)
    {
        return $res = $this->db->select()
                ->setDto('company_submit')
               ->from('company_submit')
               ->where('status', '=', $status)
               ->startGroup('AND')
               ->where('username', 'LIKE', "%{$search}%")
               ->orWhere('company', 'LIKE', "%{$search}%")
               ->orWhere('job', 'LIKE', "%{$search}%")
               ->orWhere('phone', 'LIKE', "%{$search}%")
               ->orWhere('email', 'LIKE', "%{$search}%")
               ->orWhere('status', 'LIKE', "%{$search}%")
               ->orWhere('lasted_answered', 'LIKE', "%{$search}%")
               ->endGroup()
               ->fetchAll();
    }

    public function delete($id)
    {
        return $this->db->delete()
            ->from('company_submit')
            ->where('id', '=', $id)
            ->execute();
    }
}
