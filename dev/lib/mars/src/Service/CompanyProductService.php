<?php
namespace Mars\Service;

class CompanyProductService extends \Gap\Service\ServiceBase
{
    protected $company_product_repo;

    public function bootstrap()
    {
        $this->company_product_repo = gap_repo_manager()->make('company_product');
    }

    public function searchExistProductSet($query)
    {
        return $this->company_product_repo->searchExistProductSet($query);
    }

    public function selectProductSet($query)
    {
        return $this->company_product_repo->selectProductSet($query);
    }

    public function delete($query)
    {
        return $this->company_product_repo->delete($query);
    }

    public function findOne($query)
    {
        return $this->company_product_repo->findOne($query);
    }

    public function save($data)
    {
        return $this->company_product_repo->create($data);
    }

    public function selectCompanySet($query)
    {
        return $this->company_product_repo->selectCompanySet($query);
    }

    public function searchExistCompanySet($query)
    {
        return $this->company_product_repo->searchExistCompanySet($query);
    }
}

