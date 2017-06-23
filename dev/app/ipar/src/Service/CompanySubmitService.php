<?php
namespace Ipar\Service;

class CompanySubmitService extends IparServiceBase
{
    public function getActivitySetAll()
    {
        return $this->repo('company_submit_repo')->getActivitySetAll();
    }

    public function getCount($type = '')
    {
        return $this->repo('company_submit_repo')->getcount($type);
    }

    public function getOneActivitySet($id = '')
    {
        return $this->repo('company_submit_repo')->getOneActivitySet($id);
    }

    public function getMarkSetById($id = '')
    {
        return $this->repo('company_submit_repo')->getMarkSetById($id);
    }

    public function addOneMark($mark_content = '', $admin = '', $uid = '')
    {
        return $this->repo('company_submit_repo')->addOneMark($mark_content, $admin, $uid);
    }

    public function changeStatus($uid = '', $status = '', $admin = '')
    {
        return $this->repo('company_submit_repo')->changeStatus($uid, $status, $admin);
    }

    public function search($search = '', $status = '')
    {
        return $this->repo('company_submit_repo')->search($search, $status);
    }

    public function addSingleMsg($res = '')
    {
        $this->companySubmitNotify('service@ideapar.com', $res);
        return $this->repo('company_submit_repo')->addSingleCompanySubmitMsg($res);
    }

    public function delete($id = '')
    {
        return $this->repo('company_submit_repo')->delete($id);
    }

    public function companySubmitNotify($email = 'service@ideapar.com', $options = [])
    {
        $send = new \Gap\Mail\Driver\SendCloud(config()->get('mail.adminSubmitNotify')->all());
        $send->send([
            'api_url' => 'send_template',
            'template_invoke_name' => 'adminSubmitNotify',
            'fromname' => 'no-reply',
            'to' => $email ,
            'subject' => 'A new company submit !',
            'substitution_vars' => json_encode([
                'to' => [$email],
                'sub' => [
                    '%name%' => [$options['username']],
                    '%phone%' => [$options['phone']],
                    '%email%' => [$options['email']],
                    '%company%' => [$options['company']],
                    '%job%' => [$options['job']],
                    '%content%' => [$options['content']]
                ]
            ])
        ]);
    }
}
