<?php
namespace Mars\Service;

class CompanyIndustryTagService extends \Gap\Service\ServiceBase
{
    protected $company_industry_tag_table_repo;

    public function bootstrap()
    {
        $this->company_industry_tag_table_repo = gap_repo_manager()->make('company_industry_tag');
    }

    public function selectCompanyIndustryTagSet($query)
    {
        return $this->company_industry_tag_table_repo->selectCompanyIndustryTagSet($query);
    }

    public function searchCompanyIndustryTagSet($query)
    {
        return $this->company_industry_tag_table_repo->searchCompanyIndustryTagSet($query);
    }

    public function link($data)
    {
        return $this->company_industry_tag_table_repo->create($data);
    }

    public function findOne($query = [])
    {
        return $this->company_industry_tag_table_repo->findOne($query);
    }

    public function unlink($query = [])
    {
        return $this->company_industry_tag_table_repo->delete($query);
    }

    public function existIndustryTagCompanySet($query)
    {
        return $this->company_industry_tag_table_repo->existIndustryTagCompanySet($query);
    }
}