<?php
namespace Ipar\Ui;

class CompanySubmitController extends IparControllerBase
{
    public function submitCompanyMsg()
    {
        $receive = [
            'username' => $this->request->query->get('name'),
            'phone' => $this->request->query->get('phone'),
            'email' => $this->request->query->get('email'),
            'company' => $this->request->query->get('company'),
            'job' => $this->request->query->get('job'),
            'content' => $this->request->query->get('content')
        ];

        $res = service('company_submit')->addSingleMsg($receive);
        return '';
    }
}
