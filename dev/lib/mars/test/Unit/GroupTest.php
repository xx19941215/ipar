<?php
namespace Mars\Test\Unit;

class GroupTest extends \PHPUnit_Framework_TestCase
{

    public function testGroup()
    {
        $company = Service('company');

        $db = db_manager()->get('default');

        $owner_uid = 1;

        $create = $company->createCompany([
            'owner_uid' => $owner_uid,
            'type' => 1,
            'status' => 1,
            'locale_id' => 1,
            'logo' => 'nonee',
            'title' => "joess test",
            'content' => "test ss ssssstest",
            'reg_address_id' => 1,
            'reg_date' => '2015-05-12',
            'legal_uid' => 1
            ]);
        $this->assertEquals(1, $create->ok);

        $gid = $create->items['gid'];

        $update = $company->updateCompany([
            'gid' =>$gid,
            'owner_uid' => 47,
            'type' => 1,
            'locale_id' => 1,
            'logo' => 'nonee',
            'status' => 1,
            'title' => "test",
            'reg_address_id' => 11,
            'reg_date' => '2016-06-01',
            'legal_uid' => 11
            ]);
        $this->assertEquals(1, $update->ok);
        $this->assertNull($update->errors);

        $group = $db->select()
            ->from('group')
            ->Where('gid', '=', $gid)
            ->fetchOne();
        $this->assertEquals($gid, $group->gid);
        $this->assertEquals(47, $group->owner_uid);
        $this->assertEquals(1, $group->status);

        $group_main = $db->select()
            ->from('group_main')
            ->Where('gid', '=', $gid)
            ->fetchOne();
        $this->assertEquals($gid, $group_main->gid);
        $this->assertEquals('test', $group_main->title);


        $company_db = $db->select()
            ->from('company')
            ->Where('gid', '=', $gid)
            ->fetchOne();

        $this->assertEquals(11, $company_db->reg_address_id);
        $this->assertEquals(11, $company_db->legal_uid);



        //test group_website
        $group_website = Service('group_website');
        $create_website = $group_website->createGroupWebsite([
            'gid' => $gid,
            'locale_id' => 1,
            'url' => 'http://www.baidu.com',
            'title' => 'bd'
            ]);
        $id = $create_website->getitem('group_website_id');
        $delete_website = $group_website->deleteGroupWebsite([
            'gid' => $gid,
            'id' => $id
            ]);

        //test group_social
        $group_social = Service('group_social');
        $create_social = $group_social->createGroupSocial([
            'gid' => $gid,
            'social_id' => 1,
            'url' => 'http://www.baidu.com'
            ]);
        $id = $create_social->getitem('group_social_id');
        $update_social = $group_social->updateGroupSocial([
            'id' => $id,
            'gid' => $gid,
            'social_id' => 2,
            'url' => 'http://www.baidu.com'
            ]);
        $db_test = $db->select()
            ->from('group_social')
            ->where('gid', '=', $gid, 'int')
            ->andWhere('id', '=', $id, 'int')
            ->fetchOne();
        $this->assertEquals(2, $db_test->social_id);
        $delete_social = $group_social->deleteGroupSocial([
            'gid' => $gid,
            'id' => $id
            ]);

        // test group_office
        $group_office = Service('group_office');
        $create_office = $group_office->createGroupOffice([
            'gid' => $gid,
            'office_address_id' => 1
           ]);
        $id = $create_office->getitem('group_office_id');
        $update_office = $group_office->updateGroupOffice([
            'id' => $id,
            'gid' => $gid,
            'office_address_id' => 2
            ]);
        $db_test = $db->select()
            ->from('group_office')
            ->where('gid', '=', $gid, 'int')
            ->andWhere('id', '=', $id, 'int')
            ->fetchOne();
        $this->assertEquals(2, $db_test->office_address_id);
        $delete_office = $group_office->deleteGroupOffice([
            'gid' => $gid,
            'id' => $id
            ]);

        //test group_user
        $group_user = Service('group_user');
        $create_user = $group_user->createGroupUser([
            'gid' => $gid,
            'uid' => 47,
            'roles' => 'king',
            'start' => '2016-06-01',
            'end' => '2016-06-01'
           ]);

        $id = $create_user->getitem('group_user_id');
        $update_user = $group_user->updateGroupUser([
            'id' => $id,
            'gid' => $gid,
            'uid' => 47,
            'roles' => 'joe',
            'start' => '2016-06-01',
            'end' => '2016-06-01'
            ]);

        $db_test = $db->select()
            ->from('group_user')
            ->where('gid', '=', $gid, 'int')
            ->andWhere('id', '=', $id, 'int')
            ->fetchOne();

        $this->assertEquals('joe', $db_test->roles);

        $delete_user = $group_user->deleteGroupUser([
            'gid' => $gid,
            'id' => $id
            ]);


        $update_none = $company->updateCompany([
            'gid' =>55555,
            'owner_uid' => 47,
            'type' => 1,
            'locale_id' => 1,
            'logo' => 'nonee',
            'status' => 1,
            'title' => "test",
            'reg_address_id' => 11,
            'reg_date' => '2016-06-01',
            'legal_uid' => 11
            ]);
        $this->assertEquals(0, $update_none->ok);
        $this->assertArrayHasKey('gid', $update_none->errors);


        $actevate = $company->activateGroupByGid($gid);

        $this->assertEquals(1, $actevate->ok);

        $actevate_status = $db->select()
            ->from('group')
            ->Where('gid', '=', $gid, 'int')
            ->fetchOne()->status;

        $this->assertEquals(1, $actevate_status);

        $delete_activate = $company->deleteCompanyByGid($gid);
        $this->assertEquals(0, $delete_activate->ok);
        $this->assertArrayHasKey('gid', $delete_activate->errors);

        $actevate_none = $company->activateGroupByGid(11111);
        $this->assertEquals(0, $actevate_none->ok);
        $this->assertArrayHasKey('gid', $actevate_none->errors);

        $deactivate = $company->deactivateGroupByGid($gid);
        $this->assertEquals(1, $deactivate->ok);
        $deactivate_status = $db->select()
            ->from('group')
            ->Where('gid', '=', $gid, 'int')
            ->fetchOne()->status;

        $this->assertEquals(0, $deactivate_status);

        $delete_deactivate = $company->deleteCompanyByGid($gid);
        $this->assertEquals(1, $delete_deactivate->ok);

        $delete_group = $db->select()
            ->from('group')
            ->Where('gid', '=', $gid, 'int')
            ->fetchOne();
        $this->assertFalse($delete_group);

        $delete_group_main = $db->select()
            ->from('group_main')
            ->Where('gid', '=', $gid, 'int')
            ->fetchOne();
        $this->assertFalse($delete_group_main);

        $delete_company = $db->select()
            ->from('company')
            ->Where('gid', '=', $gid, 'int')
            ->fetchOne();
        $this->assertFalse($delete_company);
    }
}
