<?php
namespace Mars\Service;

class CompanyBrandTagService extends \Gap\Service\ServiceBase
{
    protected $company_brand_tag_repo;

    public function bootstrap()
    {
        $this->company_brand_tag_repo = gap_repo_manager()->make('company_brand_tag');
    }

    public function save($data)
    {
        return $this->company_brand_tag_repo->save($data);
    }

    public function getCompanyBrandTagSet($query)
    {
        return $this->company_brand_tag_repo->getCompanyBrandTagSet($query);
    }

    public function findOne($query)
    {
        return $this->company_brand_tag_repo->findOne($query);
    }

    public function unlink($data)
    {
        return $this->company_brand_tag_repo->unlink($data);
    }
}
