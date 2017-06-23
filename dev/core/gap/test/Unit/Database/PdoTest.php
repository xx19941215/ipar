<?php
namespace Gap\Test\Unit;

class PdoTest extends \PHPUnit_Framework_TestCase
{
    public function testSelectSqlBuilder()
    {
        $db = db_manager()->get('default');

        $bd = $db->select();

        $start_date = '2016-03-25 00:00:00';
        $end_date = '2016-03-26 00:00:00';

        $bd->from('story')
            ->where('created', '>=', $start_date)
            ->andWhere('created', '<', $end_date);

        $stories = $bd->fetchAll();

        $this->assertEquals(
            'SELECT * FROM `story` WHERE `created` >= :created AND `created` < :created_2 LIMIT 10',
            $bd->getExecutedSql()
        );

        $start = strtotime($start_date);
        $end = strtotime($end_date);

        foreach ($stories as $story) {
            $this->assertLessThan($end, strtotime($story->created));
            $this->assertGreaterThanOrEqual($start, strtotime($story->created));
        }

        $this->assertLessThanOrEqual(10, count($stories));

        $bd->limit(0);
        $stories = $bd->fetchAll();
        $this->assertEquals(
            'SELECT * FROM `story` WHERE `created` >= :created AND `created` < :created_2',
            $bd->getExecutedSql()
        );
    }

    public function testSqlBuilder()
    {
        $db = db_manager()->get('default');

        $this->assertNotFalse($db->exec('DROP TABLE IF EXISTS `test_user`;'));
        $this->assertNotFalse($db->exec('DROP TABLE IF EXISTS `test_user_role`;'));

        $this->assertNotFalse($db->exec('CREATE TABLE IF NOT EXISTS `test_user` (
            `uid` INT NOT NULL AUTO_INCREMENT,
            `host` VARCHAR(42) NOT NULL,
            `username` VARCHAR(128) NOT NULL,
            `password` VARCHAR(256) NOT NULL ,
            `changed` TIMESTAMP NOT NULL,
            PRIMARY KEY (`uid`)
        );'));
        $this->assertNotFalse($db->exec('CREATE TABLE IF NOT EXISTS `test_user_role` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `uid` INT NOT NULL,
            `role` VARCHAR(42) NOT NULL,
            `hit` INT NOT NULL DEFAULT 0,
            `changed` TIMESTAMP NOT NULL,
            PRIMARY KEY (`id`)
        );'));

        $builder = $db->insert('test_user');
        $builder->value('host', 'localhost')->value('username', 'tester1')->value('password', 'p123');

        $this->assertEquals(
            'INSERT INTO `test_user` (`host`, `username`, `password`) VALUES (:host, :username, :password)',
            $builder->buildInsertSql()
        );
        $this->assertEquals(
            [
                ['param' => ':host', 'value' => 'localhost', 'type' => 'str'],
                ['param' => ':username', 'value' => 'tester1', 'type' => 'str'],
                ['param' => ':password', 'value' => 'p123', 'type' => 'str'],
            ],
            $builder->getBindValues()
        );

        $this->assertTrue($builder->execute());
        $uid1 = $db->lastInsertId();
        $this->assertGreaterThan(0, $uid1);

        $this->assertTrue($db->insert('test_user')->value('host', '127.0.0.1')->value('username', 'test2')->value('password', 'haha')->execute());
        $uid2 = $db->lastInsertId();
        $this->assertGreaterThan(0, $uid2);

        $this->assertTrue($db->insert('test_user_role')->value('uid', $uid1, 'int')->value('role', 'dev')->execute());
        $this->assertTrue($db->insert('test_user_role')->value('uid', $uid1, 'int')->value('role', 'tester')->execute());
        $this->assertTrue($db->insert('test_user_role')->value('uid', $uid1, 'int')->value('role', 'hello')->execute());

        $builder = $db->select(['u', 'uid'], ['u', 'username'], ['r', 'role'])->from(['test_user', 'u'], ['test_user_role', 'r'])->where(['u', 'host'], '=', 'localhost')->andWhereRaw("`u`.`uid` = `r`.`uid`");
        $this->assertEquals(
            [['param' => ':u_host', 'value' => 'localhost', 'type' => 'str']],
            $builder->getBindValues()
        );
        $this->assertEquals(
            "SELECT `u`.`uid`, `u`.`username`, `r`.`role` FROM `test_user` `u`, `test_user_role` `r` WHERE `u`.`host` = :u_host AND `u`.`uid` = `r`.`uid` LIMIT 10",
            $builder->buildSelectSql()
        );
        $this->assertEquals(3, count($builder->fetchAll()));

        $builder = $db->select()->from(['test_user_role', 'r'])->leftJoin(['test_user', 'u'], ['u', 'uid'], '=', ['r', 'uid'])->where(['u', 'uid'], '=', $uid1, 'int');
        $this->assertEquals(
            "SELECT * FROM `test_user_role` `r` LEFT JOIN `test_user` `u` ON `u`.`uid` = `r`.`uid` WHERE `u`.`uid` = :u_uid LIMIT 10",
            $builder->buildSelectSql()
        );

        $builder = $db->update(['test_user_role', 'r'])->leftJoin(['test_user', 'u'], ['u', 'uid'], '=', ['r', 'uid'])->incr(['r', 'hit'])->where(['u', 'uid'], '=', $uid1, 'int');
        $this->assertEquals(
            "UPDATE `test_user_role` `r` LEFT JOIN `test_user` `u` ON `u`.`uid` = `r`.`uid` SET `r`.`hit` = `r`.`hit` + 1 WHERE `u`.`uid` = :u_uid",
            $builder->buildUpdateSql()
        );
        $this->assertTrue($builder->execute());

        $builder = $db->delete('u', 'r')->from(['test_user_role', 'r'])->leftJoin(['test_user', 'u'], ['u', 'uid'], '=', ['r', 'uid'])->where(['r', 'hit'], '>', 0, 'int');
        $this->assertEquals(
            "DELETE `u`, `r` FROM `test_user_role` `r` LEFT JOIN `test_user` `u` ON `u`.`uid` = `r`.`uid` WHERE `r`.`hit` > :r_hit",
            $builder->buildDeleteSql()
        );
        $this->assertTrue($builder->execute());

        $user_objs = $db->select()->from('test_user')->fetchAll();
        $role_objs = $db->select()->from('test_user_role')->fetchAll();

        $this->assertNotFalse($db->exec('DROP TABLE `test_user`;'));
        $this->assertNotFalse($db->exec('DROP TABLE `test_user_role`;'));
    }
}
