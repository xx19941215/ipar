<?php
namespace Admin\Ui;

class GroupLogoController extends AdminControllerBase
{

    public function edit()
    {
        $group = $this->getGroupFromParam();

        return $this->page('group/upload_logo', [
            'group' => $group
        ]);
    }

    public function editPost()
    {
        if (!$img_file = $this->request->files->get('logo')) {
            die('Empty Logo file! Please select one!');
        }

        $gid = $this->request->request->get('gid');
        $logo = json_encode($this->uploadLogo($img_file));
        $pack = $this->service('group')->updateField(['gid' => $gid], 'logo', $logo);
        $type = get_type_key($this->service('group')->findOne(['gid' => $gid])->type_id);

        if ($pack->isOk()) {
            return $this->gotoRoute("admin-ui-$type-show", [
                'gid' => $gid
            ]);
        }
        return $this->page('group/upload_logo');
    }

    protected function getGroupFromParam()
    {
        $gid = $this->getParam('gid');
        $group = $this->service('group')->findOne(['gid' => $gid]);

        if (!$group) {
            die('error request');
        }

        return $group;
    }

    protected function uploadLogo($img_file)
    {
        $img_tool = image_tool();
        $pack = $img_tool->save($img_file);

        if (!$pack->isOk()) {
            return $pack->toArray();
        }

        $image = $pack->getItem('image');
        $image->resize('small', ['w' => 100]);
        $image->resize('large', ['w' => 600]);
        $image->resize('cover', ['w' => 200, 'h' => 123]);

        return [
            'site' => config()->get('img.site'),
            'dir' => $image->dir,
            'name' => $image->name,
            'ext' => $image->ext
        ];
    }
}