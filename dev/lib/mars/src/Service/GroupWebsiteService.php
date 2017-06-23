<?php
namespace Mars\Service;

class GroupWebsiteService extends MarsServiceBase
{
    public function createGroupWebsite($data = [])
    {
        $validate = $this->validate($data);
        if (!$validate->isOk()) {
            return $validate;
        }
        $gid = prop($data, 'gid', 0);
        if (!service('company')->findCompanyByGid($gid)) {
            return $this->packError('gid', 'gid is not exists');
        }
        return $this->repo('group_website')->createGroupWebsite($data);
    }

    public function updateGroupWebsite($data = [])
    {
        $validate = $this->validate($data);
        if (!$validate->isOk()) {
            return $validate;
        }
        return $this->repo('group_website')->updateGroupWebsite($data);
    }

    public function schGroupWebsiteSet($query = [])
    {
        return $this->repo('group_website')->schGroupWebsiteSet($query);
    }

    public function deleteGroupWebsite($query = [])
    {
        return $this->repo('group_website')->deleteGroupWebsite($query);
    }

    public function validate($data = [])
    {
        $gid = prop($data, 'gid', 0);
        $url = prop($data, 'url', '');
        $title = prop($data, 'title', '');

        if ($gid == 0 || !is_numeric($gid)) {
            return $this->packError('gid', 'gid-error');
        }

        if (empty($url)) {
            return $this->packError('url', 'url-error');
        }
        if (!is_string($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->packError('url', 'url-error');
        }

        if (empty($title) || !is_string($title)) {
            return $This->packError('title', 'title-empty');
        }
        return $this->packOk();
    }
}
