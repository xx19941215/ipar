<?php

namespace Mars\Repo;

class GroupSocialRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'group_social';
    protected $dto = 'group_social';
    protected $fields = [
        'id' => 'int',
        'gid' => 'int',
        'social_id' => 'int',
        'name' => 'str',
        'url' => 'str',
        'qrcode' => 'str',
        'changed' => 'str',
    ];

    public function search($query = [])
    {
        $ssb = $this->db->select()
            ->from($this->table_name)
            ->setDto($this->dto);

        if ($gid = prop($query, 'gid', '')) {
            $ssb->where('gid', '=', "$gid");
        }
        $ssb->orderBy('changed', 'desc');
        return $this->dataSet($ssb);
    }

    protected function validate($data)
    {
        $gid = prop($data, 'gid');
        if (!is_numeric($gid) || empty($gid)) {
            $this->addError('gid', 'empty');
            return false;
        }

        $name = prop($data, 'name');
        if (!is_string($name) || empty($name)) {
            $this->addError('name', 'empty');
            return false;
        }

        $name_length = mb_strlen($name);
        if (($name_length > 0 && $name_length < 2) || $name_length > 120) {
            $this->addError('name', ['out-range-%d-and-%d', 2, 120]);
            return false;
        }

        $social_id = prop($data, 'social_id');
        if (!is_numeric($social_id) || empty($social_id) || ($social_id <= 0) || ($social_id > 8)) {
            $this->addError('social_id', 'error');
            return false;
        }

        $gid = prop($data, 'gid');
        if ($existed = $this->findOne(['gid' => $gid, 'social_id' => $social_id], ['id'])) {
            if ($existed->id != prop($data, 'id', 0)) {
                $this->addError('social_id', 'already-exists');
                return false;
            }
        }

        $url = prop($data, 'url');
        $url_length = mb_strlen($url);

        if ((($url_length > 0 && $url_length < 2) || $url_length > 120) && !filter_var($url, FILTER_VALIDATE_URL)) {
            $this->addError('url', 'error');
            return false;
        }

    }
}

