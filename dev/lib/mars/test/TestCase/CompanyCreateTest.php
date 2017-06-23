<?php
namespace Mars\Test\TestCase;

class CompanyCreateTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/company-create-init.xml');
    }


    public function testCreate()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'ideapar',
            'fullname' => 'ideapar.com',
            'content' => 'ideapar-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'group_id' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => '14',
            'is_claimed' => '1'
        ];
        $pack = $company_repo->create($data);
        $this->assertTrue($pack->isOk());

        if ($pack->isOk()) {
            $query_table = $this->getConnection()->createQueryTable(
                'company',
                'SELECT gid, legal_person, reg_address, company_address, email, admin_uid, is_claimed FROM `company`'
            );
            $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) .
                '/data/company-create-expected.xml')->getTable('company');
            $this->assertTablesEqual($expected_table, $query_table);

            $query_table = $this->getConnection()->createQueryTable(
                'group',
                'SELECT gid, uid, type_id, name, fullname, content, logo, website, size_range_id, status, established, type_id FROM `group`'
            );
            $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) .
                '/data/company-create-expected.xml')->getTable('group');
            $this->assertTablesEqual($expected_table, $query_table);
        }
    }

    public function testErrorAlreadyExist()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'company-name',
            'fullname' => 'company-fullname',
            'content' => 'content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => '14',
            'is_claimed' => '1'
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('already-exists', $pack->getError('fullname'));
    }

    public function testErrorTooShortFullname()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'company-name',
            'fullname' => 'c',
            'content' => 'content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => '14',
            'is_claimed' => '1'
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('fullname'));
    }

    public function testErrorTooLongFullname()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'company-name',
            'fullname' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'content' => 'content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => '14',
            'is_claimed' => '1'
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('fullname'));
    }

    public function testErrorEmptyFullname()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'company-name',
            'fullname' => '',
            'content' => 'content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => '14',
            'is_claimed' => '1'
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('fullname'));
    }

    /*
    public function testErrorEstablished()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'company-name',
            'fullname' => 'idea',
            'content' => 'content',
            'established' => 'aaaaa',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => '14',
            'is_claimed' => '1'
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('not-date', $pack->getError('established'));
    }

    public function testErrorContent()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'company-name',
            'fullname' => 'idea',
            'content' => '',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => '14',
            'is_claimed' => '1'
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('content'));
    }
    */

    public function testErrorType()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => 0,
            'name' => 'company-name',
            'fullname' => 'ideapar',
            'content' => 'content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => '14',
            'is_claimed' => '1'
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('type-error', $pack->getError('type'));
    }

    public function testErrorUid()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'company-name',
            'fullname' => 'ideapar',
            'content' => 'content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 0,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => '14',
            'is_claimed' => '1'
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('error', $pack->getError('uid'));
    }
    /*
    public function testErrorWebsite()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'company-name',
            'fullname' => 'ideapar',
            'content' => 'content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http:ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => '14',
            'is_claimed' => '1'
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('error', $pack->getError('website'));
    }
    */
    public function testErrorContentLength()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'company-name',
            'fullname' => 'ideapar',
            'content' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http:ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => '14',
            'is_claimed' => '1'
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals(['out-of-range-%d', 1000], $pack->getError('content'));
    }
    /*
    public function testErrorEmptyLegalPerson()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'ideapar',
            'fullname' => 'ideapar.com',
            'content' => 'ideapar-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => 14,
            'is_claimed' => 1
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals("empty", $pack->getError('legal_person'));

    }
    */
    public function testErrorLengthShortLegalPerson()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'ideapar',
            'fullname' => 'ideapar.com',
            'content' => 'ideapar-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "p",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => 14,
            'is_claimed' => 1
        ];

        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('legal_person'));
    }

    public function testErrorLengthLongLegalPerson()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'ideapar',
            'fullname' => 'ideapar.com',
            'content' => 'ideapar-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => 14,
            'is_claimed' => 1
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());

        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('legal_person'));
    }

    public function testErrorEmail()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'ideapar',
            'fullname' => 'ideapar.com',
            'content' => 'ideapar-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '335,guodingroad',
            'email' => "email.com",
            'admin_uid' => 14,
            'is_claimed' => 1
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());

        $this->assertEquals("error", $pack->getError('email'));
    }
    /*
    public function testErrorEmptyRegAddress()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'ideapar',
            'fullname' => 'ideapar.com',
            'content' => 'ideapar-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => 14,
            'is_claimed' => 1
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());

        $this->assertEquals("empty", $pack->getError('reg_address'));
    }
    */
    public function testErrorLengthShortRegAddress()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'ideapar',
            'fullname' => 'ideapar.com',
            'content' => 'ideapar-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "y",
            'company_address' => '335,guodingroad',
            'email' => "name@email.com",
            'admin_uid' => 14,
            'is_claimed' => 1
        ];
        $pack = $company_repo->create($data);

        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('reg_address'));
    }

    public function testErrorLengthLongRegAddress()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'ideapar',
            'fullname' => 'ideapar.com',
            'content' => 'ideapar-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
            'email' => "name@email.com",
            'company_address' => '335,guodingroad',
            'admin_uid' => 14,
            'is_claimed' => 1
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());

        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('reg_address'));
    }
    /*
    public function testErrorEmptyCompanyAddress()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'ideapar',
            'fullname' => 'ideapar.com',
            'content' => 'ideapar-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => '',
            'email' => "name@email.com",
            'admin_uid' => 14,
            'is_claimed' => 1
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals("empty", $pack->getError('company_address'));
    }
    */
    public function testErrorLengthShortCompanyAddress()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'ideapar',
            'fullname' => 'ideapar.com',
            'content' => 'ideapar-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'company_address' => 'd',
            'email' => "name@email.com",
            'admin_uid' => 14,
            'is_claimed' => 1
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('company_address'));
    }

    public function testErrorLengthLongCompanyAddress()
    {
        $company_repo = gap_repo_manager()->make('company');
        $data = [
            'type_id' => get_type_id('company'),
            'name' => 'ideapar',
            'fullname' => 'ideapar.com',
            'content' => 'ideapar-content',
            'established' => '2016-07-01 00:00:00',
            'logo' => '',
            'website' => 'http://ideapar.com',
            'size_range_id' => 1,
            'uid' => 14,
            'gid' => 0,
            'legal_person' => "peter",
            'reg_address' => "yangpu,shanghai",
            'email' => "name@email.com",
            'company_address' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
            aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'admin_uid' => 14,
            'is_claimed' => 1
        ];
        $pack = $company_repo->create($data);
        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('company_address'));
    }
}
