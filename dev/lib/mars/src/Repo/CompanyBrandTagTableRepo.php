<?php
namespace Mars\Repo;

class CompanyBrandTagTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'company_brand_tag';
    protected $dto = 'company_brand_tag';
    protected $fields = [
        'id' => 'int',
        'gid' => 'int',
        'tag_id' => 'int',
        'add' => 'str'
    ];

    public function validate($data)
    {
        $tag_id = prop($data, 'tag_id', '');
        $gid = prop($data, 'gid', '');

        if ($this->findOne(['tag_id' => $tag_id, 'gid' => $gid])) {
            $this->addError('brand_tag', 'already-existed');
            return false;
        }

        return true;
    }
}