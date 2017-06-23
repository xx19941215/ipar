<?php
namespace Mars\Test\Unit;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testReg()
    {
        $user_service = service('user');

        $email_ok = 'ok@ideapar.com';
        $password_ok = 'hello-password';
        $nick_ok = 'hello-nick';

        $email_errors = ['', 'f@f', 'ff@', '@fdsa'];
        $password_errors = [
            '', 'ab', '12', 'a#', '0123456789012345678912'
        ];
        $nick_errors = [
            '', '1', '#', '*f', '~#', '0123456789012345678912', '9fdsafs',
            '3hello-nick', '1贝蕾', '@fdsa'
        ];

        $this->_clearUser($user_service, $email_ok, $nick_ok);

        foreach ($email_errors as $email_error) {
            $pack = $user_service->reg($email_error, $password_ok, $nick_ok);
            $this->assertFalse($pack->isOk());
            $this->assertArrayHasKey('email', $pack->getErrors());
        }

        // password must be string between 3 and 21
        foreach ($password_errors as $password_error) {
            $pack = $user_service->reg($email_ok, $password_error, $nick_ok);
            $this->assertFalse($pack->isOk());
            $this->assertArrayHasKey('password', $pack->getErrors());
        }

        //
        // nick must be string between 1 ~ 21, 
        // can only contain character a-zA-Z0-9.-_ and chinese characters
        // first character cannot be 0-9 or ._-
        //
        foreach ($nick_errors as $nick_error) {
            $pack = $user_service->reg($email_ok, $password_ok, $nick_error);
            $this->assertFalse($pack->isOk());
            $this->assertArrayHasKey('nick', $pack->getErrors());
        }

        // reg new user, not activated
        $pack = $user_service->reg($email_ok, $password_ok, $nick_ok);
        $user = $pack->getItem('user');
        $this->assertEquals($email_ok, $user->getPrimaryEmail());
        $this->assertEquals($nick_ok, $user->nick);
        $this->assertEquals(0, $user->status);

        // email already exists
        $nick_another = 'hello-nick-2';
        $pack = $user_service->reg($email_ok, $password_ok, $nick_another);
        $this->assertFalse($pack->isOk());
        $this->assertArrayHasKey('email', $pack->getErrors());

        // nick already exists
        $email_another = 'another1234332@ideapar.com';
        $pack = $user_service->reg($email_another, $password_ok, $nick_ok);
        $this->assertFalse($pack->isOk());
        $this->assertArrayHasKey('nick', $pack->getErrors());

        $this->_clearUser($user_service, $email_ok, $nick_ok);
    }

    public function testLogin()
    {
        $email = 'test@ideapar.com';
        $password = 'hello-password3';
        $nick = 'hello-nick';

        $user_service = service('user');
        $this->_clearUser($user_service, $email, $nick);

        $pack = $user_service->reg($email, $password, $nick);
        $this->assertTrue($pack->isOk());

        $pack = $user_service->login($email, $password);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('not-active', $pack->getError('user'));

        $user = $user_service->getUserByEmail($email);
        $this->assertInstanceOf('Mars\Dto\UserDto', $user);
        $this->assertEquals(0, $user->status);

        $pack = $user_service->activateUser(['uid' => $user->uid]);
        $this->assertTrue($pack->isOk());

        $pack = $user_service->login($email, 'fff');
        $this->assertFalse($pack->isOk());
        $this->assertEquals('not-match', $pack->getError('password'));

        $pack = $user_service->login('email-not-exists@not-exists-site.com', 'fff');
        $this->assertFalse($pack->isOk());
        $this->assertEquals('not-found', $pack->getError('email'));

        $pack = $user_service->login($email, $password);
        $this->assertTrue($pack->isOk());

        $user = $pack->getItem('user');
        $this->assertEquals($email, $user->getPrimaryEmail());

        $pack = $user_service->deactivateUser(['uid' => $user->uid]);
        $this->assertTrue($pack->isOk());

        $user = $user_service->getCurrentUser();
        $this->assertEquals(0, $user->status);

        $pack = $user_service->login($email, $password);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('not-active', $pack->getError('user'));

        $this->_clearUser($user_service, $email, $nick);
    }

    public function testRepasswordByUid()
    {
        $email = 'test@ideapar.com';
        $password = 'hello-password3';
        $password_changed = 'password-changed';
        $nick = 'hello-nick';

        $user_service = service('user');
        $this->_clearUser($user_service, $email, $nick);

        $pack = $user_service->reg($email, $password, $nick);
        $this->assertTrue($pack->isOk());

        $pack = $user_service->activateUser(['email' => $email]);
        $this->assertTrue($pack->isOk());

        $pack = $user_service->login($email, $password_changed);
        $this->assertFalse($pack->isOk());

        $pack = $user_service->login($email, $password);
        $this->assertTrue($pack->isOk());

        $user = $pack->getItem('user');
        $this->assertEquals(1, $user->status);

        $pack = $user_service->repasswordByUid($user->uid, '');
        $this->assertEquals('empty', $pack->getError('password'));

        $pack = $user_service->repasswordByUid($user->uid, '33');
        $this->assertArrayHasKey('password', $pack->getErrors());

        $pack = $user_service->repasswordByUid($user->uid, $password_changed);
        $this->assertTrue($pack->isOk());

        $pack = $user_service->login($email, $password_changed);
        $this->assertTrue($pack->isOk());

        $user = $user_service->getCurrentUser();
        $this->assertEquals($email, $user->getPrimaryEmail());

        $this->_clearUser($user_service, $email, $nick);
    }

    public function testRepasswordWithEmailToken()
    {
        $email = 'test@ideapar.com';
        $password = 'hello-password3';
        $new_password = 'new-password333';
        $nick = 'hello-nick';


        $user_service = service('user');
        $this->_clearUser($user_service, $email, $nick);

        $pack = $user_service->reg($email, $password, $nick);
        $this->assertTrue($pack->isOk());

        $user = $user_service->getUserByEmail($email);
        $encoded_email = $user_service->encodeEmail($user);
        $token = $user_service->generateToken($user);

        $pack = $user_service->repasswordWithEmailToken($user, $new_password, $encoded_email, $token);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('not-active', $pack->getError('user'));

        $pack = $user_service->activateUser(['email' => $email]);
        $this->assertTrue($pack->isOk());

        $pack = $user_service->login($email, $password);
        $this->assertTrue($pack->isOk());
        $user = $pack->getItem('user');

        $pack = $user_service->repasswordWithEmailToken($user, $new_password, $encoded_email, $token);
        $this->assertTrue($pack->isOk());

        $pack = $user_service->login($email, $new_password);
        $this->assertTrue($pack->isOk());

    }

    public function testActivateWithEmailToken()
    {
        $email = 'test@ideapar.com';
        $password = 'hello-password3';
        $nick = 'hello-nick';

        $user_service = service('user');
        $this->_clearUser($user_service, $email, $nick);

        $pack = $user_service->reg($email, $password, $nick);
        $this->assertTrue($pack->isOk());
        $user = $pack->getItem('user');
        $this->assertEquals(0, $user->status);

        $pack = $user_service->login($email, $password);
        $this->assertEquals('not-active', $pack->getError('user'));

        $token = $user_service->generateToken($user);
        $encoded_email = $user_service->encodeEmail($user);
        $pack = $user_service->activateWithEmailToken($user, $encoded_email, $token);

        $pack = $user_service->login($email, $password);
        $this->assertTrue($pack->isOk());
        $user = $pack->getItem('user');
        $this->assertEquals(1, $user->status);

        $this->_clearUser($user_service, $email, $nick);

    }

    public function testDeleteUserByUid()
    {
        $email = 'test@ideapar.com';
        $password = 'hello-password3';
        $nick = 'hello-nick';

        $user_service = service('user');
        $this->_clearUser($user_service, $email, $nick);

        $pack = $user_service->reg($email, $password, $nick);
        $this->assertTrue($pack->isOk());
        $user = $pack->getItem('user');

        $pack = $user_service->activateUser(['uid' => $user->uid]);
        $this->assertTrue($pack->isOk());

        $pack = $user_service->deleteUserByUid($user->uid);
        $this->assertFalse($pack->isOk());
        $this->assertEquals('not-deactivated-user', $pack->getError('user'));

        $pack = $user_service->deactivateUser(['uid' => $user->uid]);
        $this->assertTrue($pack->isOk());

        $pack = $user_service->deleteUserByUid($user->uid);
        $this->assertTrue($pack->isOk());

        $this->_clearUser($user_service, $email, $nick);
    }

    protected function _clearUser($user_service, $email = '', $nick = '')
    {
        if ($email) {
            $uid = $user_service->findUid(['email' => $email]);
            if ($uid) {
                $user_service->deactivateUser(['uid' => $uid]);
                $user_service->deleteUserByUid($uid);
            }
        }
        if ($nick) {
            $uid = $user_service->findUid(['nick' => $nick]);
            if ($uid) {
                $user_service->deactivateUser(['uid' => $uid]);
                $user_service->deleteUserByUid($uid);
            }
        }
    }
}
