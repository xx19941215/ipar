---
title: ::repasswordByUid
parentTitle: Mars\Service\UserService


## ::repasswordByUid

```php
\Mars\Service\UserService::repasswordByUid(int $uid, string $password)
```

### parameters
- int $uid
- string $password

### return
- \Mars\ParkDto $pack

### PHPUnit
```php
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

    $pack = $user_service->activateUserByEmail($email);
    $this->assertTrue($pack->isOk());

    $pack = $user_service->login($email, $password_changed);
    $this->assertFalse($pack->isOk());

    $pack = $user_service->login($email, $password);
    $this->assertTrue($pack->isOk());

    $user = $pack->getItem('user');
    $this->assertEquals(1, $user->status);

    $pack = $user_service->repasswordByUid($user->uid, '');
    $this->assertEquals('must-not-be-empty', $pack->getError('password'));

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
```
