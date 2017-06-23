<?php
namespace Ipar\Ui;

class BrandTagController extends IparControllerBase
{
    public function show()
    {
        $tag = $this->getBrandTagFromParam();

        if (!$tag) {
            return $this->page('404/show');
        }

        $company_brand_tag = $this->service('company_brand_tag')->findOne([
            'tag_id' => $tag->id
        ]);
        $company = $this->service('company')->findOne([
            'gid' => $company_brand_tag->gid
        ]);

        return $this->page('brand_tag/show', [
            'tag' => $tag,
            'company' => $company,
            'company_brand_tag' => $company_brand_tag,
            'header_menu' => 'ipar-ui-company-index'
        ]);
    }

    public function getBrandTagFromParam()
    {
        $tag = $this->service('tag')->getTagByZcode($this->getParam('zcode'));

        return $tag;
    }
}