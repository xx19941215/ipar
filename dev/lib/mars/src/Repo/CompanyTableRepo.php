<?php

namespace Mars\Repo;

class CompanyTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'company';
    protected $dto = 'company';
    protected $fields = [
        'gid' => 'int',
        'legal_person' => 'str',
        'reg_address' => 'str',
        'company_address' => 'str',
        'email' => 'str',
        'admin_uid' => 'int',
        'is_claimed' => 'int',
    ];

    protected function validate($data)
    {
        $legal_person = prop($data, 'legal_person');
//        if (!is_string($legal_person) || empty($legal_person)) {
//            $this->addError('legal_person', 'empty');
//            return false;
//        }

        $legal_person_length = mb_strlen($legal_person);
        if (($legal_person_length > 0 && $legal_person_length < 2) || $legal_person_length > 120) {
            $this->addError('legal_person', ['out-range-%d-and-%d', 2, 120]);
            return false;
        }

        $email = prop($data, 'email');
        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addError('email', 'error');
                return false;
            }
        }

        $reg_address = prop($data, 'reg_address');
//        if (!is_string($reg_address) || empty($reg_address)) {
//            $this->addError('reg_address', 'empty');
//            return false;
//        }

        $reg_address_length = mb_strlen($reg_address);
        if (($reg_address_length > 0 && $reg_address_length < 2) || $reg_address_length > 120) {
            $this->addError('reg_address', ['out-range-%d-and-%d', 2, 120]);
            return false;
        }

        $company_address = prop($data, 'company_address');
//        if (!is_string($company_address) || empty($company_address)) {
//            $this->addError('company_address', 'empty');
//            return false;
//        }

        $company_address_length = mb_strlen($company_address);
        if (($company_address_length > 0 && $company_address_length < 2) || $company_address_length > 120) {
            $this->addError('company_address', ['out-range-%d-and-%d', 2, 120]);
            return false;
        }
    }
}
