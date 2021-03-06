<?php
namespace Mars\Test\TestCase;

class GroupSocialCreateTest extends \Gap\Database\Test\DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-social-create-init.xml');
    }

    public function testCreate()
    {

        $group_social_repo = gap_repo_manager()->make('group_social');
        $pack = $group_social_repo->create([
            'gid' => 1,
            'social_id' => 3,
            'name' => 'ideapar',
            'url' => 'https://www.facebook.com/ideaparcom'
        ]);
        $this->assertTrue($pack->isOk());

        $query_table = $this->getConnection()->createQueryTable(
            'group_social',
            'SELECT gid, social_id, name, url, qrcode FROM `group_social`'
        );
        $expected_table = $this->createFlatXMLDataSet(dirname(__FILE__) . '/data/group-social-create-expected.xml')
            ->getTable('group_social');
        $this->assertTablesEqual($expected_table, $query_table);
    }

    public function testErrorEmptyGroupId()
    {
        $group_social_repo = gap_repo_manager()->make('group_social');
        $pack = $group_social_repo->create(['id' => 1], [
            'gid' => '',
            'social_id' => 4,
            'name' => 'ideapar',
            'url' => 'https://twitter.com/ideaparcom'
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('gid'));
    }

    public function testErrorEmptyName()
    {
        $group_social_repo = gap_repo_manager()->make('group_social');
        $pack = $group_social_repo->create([
            'gid' => 1,
            'social_id' => 4,
            'name' => '',
            'url' => 'https://twitter.com/ideaparcom'
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('name'));
    }

    public function testErrorTooShortName()
    {
        $group_social_repo = gap_repo_manager()->make('group_social');
        $pack = $group_social_repo->create([
            'gid' => 1,
            'social_id' => 4,
            'name' => 'i',
            'url' => 'https://twitter.com/ideaparcom'
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('name'));
    }

    public function testErrorTooLongName()
    {
        $group_social_repo = gap_repo_manager()->make('group_social');
        $pack = $group_social_repo->create([
            'gid' => 1,
            'social_id' => 4,
            'name' => 'iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii
            iiiiiiiiiiiiiiiiiiiiiiii',
            'url' => 'https://twitter.com/ideaparcom'
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('name'));
    }

    public function testErrorEmptySocialId()
    {
        $group_social_repo = gap_repo_manager()->make('group_social');
        $pack = $group_social_repo->create([
            'gid' => 1,
            'social_id' => '',
            'name' => 'ideapar',
            'url' => 'https://twitter.com/ideaparcom'
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('error', $pack->getError('social_id'));
    }

    public function testErrorExistedSocialId()
    {
        $group_social_repo = gap_repo_manager()->make('group_social');
        $pack = $group_social_repo->create([
            'gid' => 1,
            'social_id' => 2,
            'name' => 'ideapar',
            'url' => 'https://twitter.com/ideaparcom'
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('already-exists', $pack->getError('social_id'));
    }

    public function testErrorSocialId()
    {
        $group_social_repo = gap_repo_manager()->make('group_social');
        $pack = $group_social_repo->create([
            'gid' => 1,
            'social_id' => 0,
            'name' => 'ideapar',
            'url' => 'https://twitter.com/ideaparcom'
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('error', $pack->getError('social_id'));
    }

    public function testErrorEmptyUrl()
    {
        $group_social_repo = gap_repo_manager()->make('group_social');
        $pack = $group_social_repo->create([
            'gid' => 1,
            'social_id' => 4,
            'name' => 'ideapar',
            'url' => ''
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('empty', $pack->getError('url'));
    }

    public function testErrorTooShortUrl()
    {
        $group_social_repo = gap_repo_manager()->make('group_social');
        $pack = $group_social_repo->create([
            'gid' => 1,
            'social_id' => 4,
            'name' => 'ideapar',
            'url' => 'i'
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('url'));
    }

    public function testErrorTooLongUrl()
    {
        $group_social_repo = gap_repo_manager()->make('group_social');
        $pack = $group_social_repo->create([
            'gid' => 1,
            'social_id' => 4,
            'name' => 'ideapar',
            'url' => 'iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii
            iiiiiiiiiiiiiiiiiiiiiiii'
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals(["out-range-%d-and-%d", 2, 120], $pack->getError('url'));
    }

    public function testErrorUrl()
    {
        $group_social_repo = gap_repo_manager()->make('group_social');
        $pack = $group_social_repo->create([
            'gid' => 1,
            'social_id' => 4,
            'name' => 'ideapar',
            'url' => 'http:ideapar'
        ]);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('error', $pack->getError('url'));
    }
}
