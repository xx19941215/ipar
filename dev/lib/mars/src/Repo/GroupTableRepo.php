<?php

namespace Mars\Repo;

class GroupTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'group';
    protected $dto = 'group';
    protected $fields = [
        'gid' => 'int',
        'uid' => 'int',
        'type_id' => 'int',
        'zcode' => 'str',
        'name' => 'str',
        'fullname' => 'str',
        'content' => 'str',
        'established' => 'str',
        'logo' => 'str',
        'website' => 'str',
        'size_range_id' => 'int',
        'locale_id' => 'int',
        'status' => 'int'
    ];

    public function search($query = [])
    {
        $ssb = $this->db->select()
            ->from($this->table_name)
            ->setDto($this->dto);

        if ($search = prop($query, 'search', '')) { //搜索框不为空
            $ssb->where('fullname', 'LIKE', "%$search%");
            if ($type_id = prop($query, 'type_id', '')) { //类型已选择
                $ssb->andWhere('type_id', '=', "$type_id");
                $ssb->orderBy('changed', 'desc');
                return $this->dataSet($ssb);
            }
        } else { //搜索框为空
            if ($type_id = prop($query, 'type_id', '')) { //类型已选择
                $ssb->where('type_id', '=', "$type_id");
            }
        }
        $ssb->orderBy('changed', 'desc');
        return $this->dataSet($ssb);
    }

    protected function validate($data)
    {
        $fullname = prop($data, 'fullname');
        if (!is_string($fullname) || empty($fullname)) {
            $this->addError('fullname', 'empty');
            return false;
        }

        $fullname_length = mb_strlen($fullname);
        if (($fullname_length > 0 && $fullname_length < 2) || $fullname_length > 120) {
            $this->addError('fullname', ['out-range-%d-and-%d', 2, 120]);
            return false;
        }

        $uid = prop($data, 'uid');
        if (!is_numeric($uid) || empty($uid) || ($uid <= 0)) {
            $this->addError('uid', 'error');
            return false;
        }

        if ($existed = $this->findOne(['fullname' => $fullname], ['gid'])) {
            if ($existed->gid != prop($data, 'gid')) {
                $this->addError('fullname', 'already-exists');
                return false;
            }
        }

        $content = prop($data, 'content');
        $content_length = mb_strlen($content);
        if ($content_length > 1000) {
            $this->addError('content', ['out-of-range-%d', 1000]);
            return false;
        }

        $type_id = prop($data, 'type_id');
        if ($type_id <= 0) {
            $this->addError('type', 'type-error');
            return false;
        }

        $url = prop($data, 'website');
        if (!empty($url)) {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $this->addError('website', 'error');
                return false;
            }
        }
    }
}
