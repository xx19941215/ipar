<?php

$this->setCurrentSite('admin')
    ->setCurrentAccess('super')
    // company

    ->get('/company/{gid:[0-9]+}', ['as' => 'admin-ui-company-show', 'action' => 'Admin\Ui\CompanyController@show'])
    ->get('/company/add', ['as' => 'admin-ui-company-add', 'action' => 'Admin\Ui\CompanyController@add'])
    ->post('/company/add', ['as' => 'admin-ui-company-add-post', 'action' => 'Admin\Ui\CompanyController@addPost'])
    ->get('/company/{gid:[0-9]+}/edit', ['as' => 'admin-ui-company-edit', 'action' =>
        'Admin\Ui\CompanyController@edit'])
    ->post('/company/{gid:[0-9]+}/edit', ['as' => 'admin-ui-company-edit-post', 'action' =>
        'Admin\Ui\CompanyController@editPost'])
    ->get('/company/{gid:[0-9]+}/deactivate', ['as' => 'admin-ui-company-deactivate', 'action' => 'Admin\Ui\CompanyController@deactivate'])
    ->post('/company/{gid:[0-9]+}/deactivate', ['as' => 'admin-ui-company-deactivate-post', 'action' => 'Admin\Ui\CompanyController@deactivatePost'])
    ->get('/company/{gid:[0-9]+}/activate', ['as' => 'admin-ui-company-activate', 'action' => 'Admin\Ui\CompanyController@activate'])
    ->post('/company/{gid:[0-9]+}/activate', ['as' => 'admin-ui-company-activate-post', 'action' => 'Admin\Ui\CompanyController@activatePost'])
    ->get('/company/{gid:[0-9]+}/delete', ['as' => 'admin-ui-company-delete', 'action' => 'Admin\Ui\CompanyController@delete'])
    ->post('/company/{gid:[0-9]+}/delete', ['as' => 'admin-ui-company-delete-post', 'action' => 'Admin\Ui\CompanyController@deletePost'])
    ->get('/company/{gid:[0-9]+}/logo', ['as' => 'admin-ui-company-logo', 'action' =>
        'Admin\Ui\GroupLogoController@edit'])
    ->post('/company/{gid:[0-9]+}/logo', ['as' => 'admin-ui-company-logo_post', 'action' =>
        'Admin\Ui\GroupLogoController@editPost'])
    //company contact
    ->get('/company/{gid:[0-9]+}/contact', ['as' => 'admin-ui-company_contact-list', 'action' => 'Admin\Ui\CompanyContactController@list'])
    ->get('/company/{gid:[0-9]+}/contact/add', ['as' => 'admin-ui-company_contact-add', 'action' => 'Admin\Ui\CompanyContactController@add'])
    ->post('/company/{gid:[0-9]+}/contact/add', ['as' => 'admin-ui-company_contact-add-post', 'action' => 'Admin\Ui\CompanyContactController@addPost'])
    ->get('/company/{gid:[0-9]+}/contact/{group_contact_id:[0-9]+}/edit', ['as' => 'admin-ui-company_contact-edit', 'action' => 'Admin\Ui\CompanyContactController@edit'])
    ->post('/company/{gid:[0-9]+}/contact/{group_contact_id:[0-9]+}/edit', ['as' => 'admin-ui-company_contact-edit-post', 'action' => 'Admin\Ui\CompanyContactController@editPost'])
    ->get('/company/{gid:[0-9]+}/contact/{group_contact_id:[0-9]+}/delete', ['as' => 'admin-ui-company_contact-delete', 'action' => 'Admin\Ui\CompanyContactController@delete'])
    ->post('/company/{gid:[0-9]+}/contact/{group_contact_id:[0-9]+}/delete', ['as' => 'admin-ui-company_contact-delete-post', 'action' => 'Admin\Ui\CompanyContactController@deletePost'])
    //company office
    ->get('/company/{gid:[0-9]+}/office', ['as' => 'admin-ui-company_office-list', 'action' => 'Admin\Ui\CompanyOfficeController@list'])
    ->get('/company/{gid:[0-9]+}/office/add', ['as' => 'admin-ui-company_office-add', 'action' => 'Admin\Ui\CompanyOfficeController@add'])
    ->post('/company/{gid:[0-9]+}/office/add', ['as' => 'admin-ui-company_office-add-post', 'action' => 'Admin\Ui\CompanyOfficeController@addPost'])
    ->get('/company/{gid:[0-9]+}/office/{group_office_id:[0-9]+}/edit', ['as' => 'admin-ui-company_office-edit', 'action' => 'Admin\Ui\CompanyOfficeController@edit'])
    ->post('/company/{gid:[0-9]+}/office/{group_office_id:[0-9]+}/edit', ['as' => 'admin-ui-company_office-edit-post', 'action' => 'Admin\Ui\CompanyOfficeController@editPost'])
    ->get('/company/{gid:[0-9]+}/office/{group_office_id:[0-9]+}/delete', ['as' => 'admin-ui-company_office-delete', 'action' => 'Admin\Ui\CompanyOfficeController@delete'])
    ->post('/company/{gid:[0-9]+}/office/{group_office_id:[0-9]+}/delete', ['as' => 'admin-ui-company_office-delete-post', 'action' => 'Admin\Ui\CompanyOfficeController@deletePost'])
    //company social
    ->get('/company/{gid:[0-9]+}/social', ['as' => 'admin-ui-company_social-list', 'action' => 'Admin\Ui\CompanySocialController@list'])
    ->get('/company/{gid:[0-9]+}/social/add', ['as' => 'admin-ui-company_social-add', 'action' => 'Admin\Ui\CompanySocialController@add'])
    ->post('/company/{gid:[0-9]+}/social/add', ['as' => 'admin-ui-company_social-add-post', 'action' => 'Admin\Ui\CompanySocialController@addPost'])
    ->get('/company/{gid:[0-9]+}/social/{group_social_id:[0-9]+}/edit', ['as' => 'admin-ui-company_social-edit', 'action' => 'Admin\Ui\CompanySocialController@edit'])
    ->post('/company/{gid:[0-9]+}/social/{group_social_id:[0-9]+}/edit', ['as' => 'admin-ui-company_social-edit-post', 'action' => 'Admin\Ui\CompanySocialController@editPost'])
    ->get('/company/{gid:[0-9]+}/social/{group_social_id:[0-9]+}/delete', ['as' => 'admin-ui-company_social-delete', 'action' => 'Admin\Ui\CompanySocialController@delete'])
    ->post('/company/{gid:[0-9]+}/social/{group_social_id:[0-9]+}/delete', ['as' => 'admin-ui-company_social-delete-post', 'action' => 'Admin\Ui\CompanySocialController@deletePost'])
    //company_industry_tag
    ->get('/company/{gid:[0-9]+}/industry_tag', ['as' => 'admin-ui-company_industry_tag', 'action' => 'Admin\Ui\CompanyIndustryTagController@list'])
    ->get('/company/{gid:[0-9]+}/industry_tag/link', ['as' => 'admin-ui-company_industry_tag-link', 'action' => 'Admin\Ui\CompanyIndustryTagController@link'])
    ->post('/company/{gid:[0-9]+}/industry_tag/link', ['as' => 'admin-ui-company_industry_tag-link_post', 'action' => 'Admin\Ui\CompanyIndustryTagController@linkPost'])
    ->get('/company/{gid:[0-9]+}/industry_tag/{tag_id:[0-9]+}/unlink', ['as' => 'admin-ui-company_industry_tag-unlink', 'action' => 'Admin\Ui\CompanyIndustryTagController@unlink'])
    ->post('/company/{gid:[0-9]+}/industry_tag/{tag_id:[0-9]+}/unlink', ['as' => 'admin-ui-company_industry_tag-unlink_post', 'action' => 'Admin\Ui\CompanyIndustryTagController@unlinkPost'])
    //company product
    ->get('/company/{gid:[0-9]+}/product', ['as' => 'admin-ui-company_product-list', 'action' => 'Admin\Ui\CompanyProductController@list'])
    ->get('/company/{gid:[0-9]+}/product/link', ['as' => 'admin-ui-company_product-link', 'action' => 'Admin\Ui\CompanyProductController@link'])
    ->post('/company/{gid:[0-9]+}/product/add', ['as' => 'admin-ui-company_product-add_post', 'action' => 'Admin\Ui\CompanyProductController@addPost'])
    ->post('/company/{gid:[0-9]+}/product/link', ['as' => 'admin-ui-company_product-link_post', 'action' => 'Admin\Ui\CompanyProductController@linkPost'])
    ->get('/company/{gid:[0-9]+}/product/{eid:[0-9]+}/unlink', ['as' => 'admin-ui-company_product-unlink', 'action' => 'Admin\Ui\CompanyProductController@unlink'])
    ->post('/company/{gid:[0-9]+}/product/{eid:[0-9]+}/unlink', ['as' => 'admin-ui-company_product-unlink_post', 'action' => 'Admin\Ui\CompanyProductController@unlinkPost'])
    //company brand tag
    ->get('/company/{gid:[0-9]+}/brand-tag', ['as' => 'admin-ui-company_brand_tag-index', 'action' => 'Admin\Ui\CompanyBrandTagController@list'])
    ->get('/company/{gid:[0-9]+}/brand-tag/save', ['as' => 'admin-ui-company_brand_tag-save', 'action' => 'Admin\Ui\CompanyBrandTagController@save'])
    ->post('/company/{gid:[0-9]+}/brand-tag/save', ['as' => 'admin-ui-company_brand_tag-save_post', 'action' => 'Admin\Ui\CompanyBrandTagController@savePost'])
    ->get('/company/{gid:[0-9]+}/brand-tag/{tag_id:[0-9]+}/unlink', ['as' => 'admin-ui-company_brand_tag-unlink', 'action' => 'Admin\Ui\CompanyBrandTagController@unlink'])
    ->post('/company/{gid:[0-9]+}/brand-tag/{tag_id:[0-9]+}/unlink', ['as' => 'admin-ui-company_brand_tag-unlink_post', 'action' => 'Admin\Ui\CompanyBrandTagController@unlinkPost'])
    //company brand-tag / product
    ->get('/company/{gid:[0-9]+}/brand-tag/{tag_id:[0-9]+}/product', ['as' => 'admin-ui-company_brand_tag_product-index', 'action' => 'Admin\Ui\BrandTagProductController@list'])
    ->get('/company/{gid:[0-9]+}/brand-tag/{tag_id:[0-9]+}/product/link', ['as' => 'admin-ui-company_brand_tag_product-link', 'action' => 'Admin\Ui\BrandTagProductController@link'])
    ->post('/company/{gid:[0-9]+}/brand-tag/{tag_id:[0-9]+}/product/link', ['as' => 'admin-ui-company_brand_tag_product-link_post', 'action' => 'Admin\Ui\BrandTagProductController@linkPost'])
    ->post('/company/{gid:[0-9]+}/brand-tag/{tag_id:[0-9]+}/product/save', ['as' => 'admin-ui-company_brand_tag_product-save_post', 'action' => 'Admin\Ui\BrandTagProductController@savePost'])
    ->get('/company/{gid:[0-9]+}/brand-tag/{tag_id:[0-9]+}/product/{eid:[0-9]+}/unlink', ['as' => 'admin-ui-company_brand_tag_product-unlink', 'action' => 'Admin\Ui\BrandTagProductController@unlink'])
    ->post('/company/{gid:[0-9]+}/brand-tag/{tag_id:[0-9]+}/product/{eid:[0-9]+}/unlink', ['as' => 'admin-ui-company_brand_tag_product-unlink_post', 'action' => 'Admin\Ui\BrandTagProductController@unlinkPost']);

