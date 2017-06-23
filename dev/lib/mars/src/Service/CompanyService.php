<?php
namespace Mars\Service;

class CompanyService extends \Gap\Service\ServiceBase
{
    protected $company_repo;

    public function bootstrap()
    {
        $this->company_repo = gap_repo_manager()->make('company');
    }

    public function search($query)
    {
        return $this->company_repo->search($query);
    }

    public function create($data)
    {
        return $this->company_repo->create($data);
    }

    public function update($query, $data)
    {
        return $this->company_repo->update($query, $data);
    }

    public function findOne($query = [], $fields = [])
    {
        return $this->company_repo->findOne($query, $fields);
    }

    public function delete($query)
    {
        return $this->company_repo->delete($query);
    }

    public function updateField($query, $field, $value)
    {
        return $this->company_repo->updateField($query, $field, $value);
    }

    public function schCompanySet($opts = [])
    {
        return $this->company_repo->schCompanySet($opts);
    }
}
