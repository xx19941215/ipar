---
title: ::deleteUserByUid
parentTitle: Mars\Service\UserService

## ::deleteUserByUid

delete **deactivated** user

```php
\Mars\Service\UserService::deleteUserByUid(int $uid)
```

### parameters
- int $uid

### return
- \Mars\ParkDto $pack


### phpunit
```php
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

    $pack = $user_service->activateUserByUid($user->uid);
    $this->assertTrue($pack->isOk());

    $pack = $user_service->deleteUserByUid($user->uid);
    $this->assertFalse($pack->isOk());
    $this->assertEquals('not-deactivated-user', $pack->getError('user'));

    $pack = $user_service->deactivateUserByUid($user->uid);
    $this->assertTrue($pack->isOk());

    $pack = $user_service->deleteUserByUid($user->uid);
    $this->assertTrue($pack->isOk());

    $this->_clearUser($user_service, $email, $nick);
}
```
