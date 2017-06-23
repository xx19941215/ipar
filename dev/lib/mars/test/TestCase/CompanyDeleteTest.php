<?php
namespace Mars\Test\TestCase;

class CompanyDeleteTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/company-delete-init.xml');
    }


    public function testDelete()
    {
        $company_repo = gap_repo_manager()->make('company');
        $gid = 1;
        $pack = $company_repo->delete($gid);

        $this->assertTrue($pack->isOk());

        if ($pack->isOk()) {

            $query_table_company = $this->getConnection()->createQueryTable(
                'company',
                'SELECT gid, legal_person, reg_address, company_address, email, admin_uid, is_claimed FROM `company`'
            );
            $expected_table_company = $this->createFlatXMLDataSet(dirname(__FILE__) .
                '/data/company-delete-expected.xml')->getTable('company');
            $this->assertTablesEqual($expected_table_company, $query_table_company);

            $query_table = $this->getConnection()->createQueryTable(
                'group',
                'SELECT gid, uid, type_id, name, fullname, content, logo, website, size_range_id, status, established, type_id FROM `group`'
            );
            $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) .
                '/data/company-delete-expected.xml')->getTable('group');
            $this->assertTablesEqual($expected_table, $query_table);
        }
    }
}