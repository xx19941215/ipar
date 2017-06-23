<?php
namespace Ipar\Rest;

class GroupController extends \Gap\Routing\Controller
{
    public function index()
    {

        $company_set = $this->service('company')
            ->search([
                'type_id' => get_type_id('company'),
                'is_activated' => true,
                'sort' => $this->request->query->get('sort')
            ])
            ->setCountPerPage(12)
            ->setCurrentPage($this->request->query->get('page'))
            ->getItems();
        $arr = [];

        foreach ($company_set as $company) {
            $item = [];
            $item['type_id'] = $company->type_id;
            $item['zcode'] = $company->zcode;
            $item['fullname'] = $company->fullname;

            if ($imgs = json_decode($company->logo, true)) {
                $item['logo'] = '<img src="' . img_src($company->getLogo(), 'small') . '">';
            }

            if ($company->reg_address) {
                $item['address'] = '<i class="icon icon-weizhi"></i>' . ((mb_strlen($company->reg_address) > 6) ?
                        (mb_substr($company->reg_address, 0, 6)) . '...' : $company->reg_address);
            }

            $item['content'] = trans('there-is-no-information');

            if ($company->content) {
                $item['content'] = mb_strlen($company->content) > 60 ? mb_substr($company->content, 0, 60) . '...' : $company->content;
            }

            $item['url'] = route_url('ipar-ui-company-show', ['zcode' => $company->zcode]);
            $arr[] = $item;
        }

        return $this->packItem('box', $arr);
    }

    public function uploadGroupLogo()
    {
        if (!$img_file = $this->request->files->get('img')) {
            return $this->badRequest();
        }

        $img_tool = image_tool();
        $post = $this->request->request;

        $pack = $img_tool->save($img_file);
        if (!$pack->isOk()) {
            return $pack;
        }

        $image = $pack->getItem('image');
        $src = [
            'x' => $post->get('src_x', 0),
            'y' => $post->get('src_y', 0),
            'w' => $post->get('src_w', 0),
            'h' => $post->get('src_h', 0)
        ];
        $image->resize('small', ['w' => 42, 'h' => 42], $src);
        $image->resize('medium', ['w' => 115, 'h' => 115], $src);
        $image->resize('cover', ['w' => 235, 'h' => 175], $src);


        $avt = [
            'site' => config()->get('img.site'),
            'dir' => $image->dir,
            'name' => $image->name,
            'ext' => $image->ext
        ];
        $img = ['logo_url' => img_src($avt, 'medium'), 'logo' => json_encode($avt)];
        return $this->packItem('avt_url', $img);
    }

    public function updateGroupLogo()
    {
        $gid = $this->request->request->get('gid');
        $logo = $this->request->request->get('logo');
        return $this->service('group')->updateGroupLogo([
            'gid' => $gid,
            'logo' => $logo
        ]);
    }

    public function editSocialPost()
    {
        $gid = $this->request->request->get('gid');
        $website_url = $this->request->request->get('website_url');
        $website_id = $this->request->request->get('website_id');
        $weibo_url = $this->request->request->get('weibo_url');
        $weibo_id = $this->request->request->get('weibo_id');
        $wechat_url = $this->request->request->get('wechat_url');
        $wechat_id = $this->request->request->get('wechat_id');
        $title = $this->request->request->get('title');

        if (!$gid) {
            return $this->packError('gid', 'empty');
        }

        $website_service = $this->service('group_website');

        if ($website_id) {
            $website = $website_service->updateGroupWebsite([
                'id' => $website_id,
                'gid' => $gid,
                'url' => $website_url,
                'title' => $title
            ]);
        } else {
            $website = $website_service->createGroupWebsite([
                'gid' => $gid,
                'locale_id' => 1,
                'url' => $website_url,
                'title' => $title
            ]);
        }
        if (!$website->isOk()) {
            return $website;
        }

        $social_service = $this->service('group_social');
        if ($weibo_id) {
            $weibo = $social_service->updateGroupSocial([
                'id' => $weibo_id,
                'gid' => $gid,
                'social_id' => 1,
                'url' => $weibo_url,
            ]);
        } else {
            $weibo = $social_service->createGroupSocial([
                'gid' => $gid,
                'social_id' => 1,
                'url' => $weibo_url
            ]);
        }
        if (!$weibo->isOk()) {
            return $weibo;
        }

        if ($wechat_id) {
            $wechat = $social_service->updateGroupSocial([
                'id' => $wechat_id,
                'gid' => $gid,
                'social_id' => 2,
                'url' => $wechat_url,
            ]);
        } else {
            $wechat = $social_service->createGroupSocial([
                'gid' => $gid,
                'social_id' => 2,
                'url' => $wechat_url
            ]);
        }
        if (!$wechat->isOk()) {
            return $wechat;
        }
        return $this->packOk();
    }

    public function createOfficePost()
    {
        $gid = $this->request->request->get('gid');
        $address = $this->request->request->get('address');
        $area_id = $this->request->request->get('area_id');
        $create_address = $this->service('address')->createAddress([
            'area_id' => $area_id,
            'title' => $address
        ]);
        if (!$create_address->isOk()) {
            return $create_address;
        }

        $create_office = $this->service('group_office')->createGroupOffice([
            'gid' => $gid,
            'office_address_id' => $create_address->getItem('address_id')
        ]);

        return $create_office;
    }

    public function updateOfficePost()
    {
        $office_id = $this->request->request->get('office_id');
        $address = $this->request->request->get('address');
        $area_id = $this->request->request->get('area_id');
        $office_address_id = $this->request->request->get('office_address_id');
        $update_address = $this->service('address')->updateAddress([
            'id' => $office_address_id,
            'area_id' => $area_id,
            'title' => $address
        ]);

        return $update_address;

    }

    public function deleteOfficePost()
    {
        $gid = $this->request->request->get('gid');
        $office_id = $this->request->request->get('office_id');

        return $this->service('group_office')->deleteGroupOffice([
            'gid' => $gid,
            'id' => $office_id
        ]);
    }

    public function createContactPost()
    {
        $gid = $this->request->request->get('gid');
        $name = $this->request->request->get('name');
        $email = $this->request->request->get('email');
        $phone = $this->request->request->get('phone');
        $roles = $this->request->request->get('roles');
        $area_id = $this->request->request->get('area_id');
        $address = $this->request->request->get('address');


        $contact_create = $this->service('group_contact')->createGroupContact([
            'gid' => $gid,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'roles' => $roles
        ]);
        if ($contact_create->isOk()) {
            return $this->packOk();
        }
        return $contact_create;
    }

    public function updateContactPost()
    {
        $gid = $this->request->request->get('gid');
        $name = $this->request->request->get('name');
        $email = $this->request->request->get('email');
        $phone = $this->request->request->get('phone');
        $roles = $this->request->request->get('roles');
        $contact_id = $this->request->request->get('contact_id');

        $update_contact = $this->service('group_contact')->updateGroupContact([
            'id' => $contact_id,
            'gid' => $gid,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'roles' => $roles
        ]);
        if ($update_contact->isOk()) {
            return $this->packOk();
        }
        return $update_contact;
    }

    public function deleteContactPost()
    {
        $gid = $this->request->request->get('gid');
        $contact_id = $this->request->request->get('contact_id');

        return $this->service('group_contact')->deleteGroupContact([
            'id' => $contact_id,
            'gid' => $gid
        ]);
    }
}
