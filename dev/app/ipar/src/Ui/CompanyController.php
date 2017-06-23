<?php
namespace Ipar\Ui;

use Gap\File\Image as FileImage;

class CompanyController extends IparControllerBase
{
    public function index()
    {
        return $this->page('company/index', [
            'header_menu' => 'ipar-ui-company-index'
        ]);
    }

    public function show()
    {
        $zcode = $this->getParam('zcode');
        $company_service = $this->service('company');
        $company = $company_service->findOne(['zcode' => $zcode]);

        if (!$company) {
            return $this->page('404/show');
        }

        $group_contact_service = $this->service('group_contact');
        $group_contact_set = $group_contact_service->search(['gid' => $company->gid]);

        $group_office_service = $this->service('group_office');
        $group_office_set = $group_office_service->search(['gid' => $company->gid]);

        $group_social_service = $this->service('group_social');
        $group_social_set = $group_social_service->search(['gid' => $company->gid]);

        $product_set = $this->service('company_product')->searchExistProductSet(['gid' => $company->gid]);
        $industry_tag_set = $this->service('company_industry_tag')->searchCompanyIndustryTagSet(['gid' => $company->gid]);
        $brand_tag_set = $this->service('company_brand_tag')->getCompanyBrandTagSet(['gid' => $company->gid]);

        return $this->page('company/show', [
            'company' => $company,
            'group_contact_set' => $group_contact_set,
            'group_office_set' => $group_office_set,
            'group_social_set' => $group_social_set,
            'product_set' => $product_set,
            'industry_tag_set' => $industry_tag_set,
            'brand_tag_set' => $brand_tag_set,
            'header_menu' => 'ipar-ui-company-index'
        ]);
    }


}
