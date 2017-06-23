---
title: ::login
parentTitle: Mars\Service\UserService


## ::login

```php
\Mars\Service\UserService::login(string $email, string $password)
```

### parameters
- string $email
- string $password

### return
- \Mars\ParkDto $pack
    - error
        - email: not-email
        - password: out-range, 3 - 21
        - password: not-match
        - user: not-active
    - ok
        - user: \Mars\Dto\UserDto

### PHPUnit

```php
<?php
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

    $pack = $user_service->activateUserByUid($user->uid);
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

    $pack = $user_service->deactivateUserByUid($user->uid);
    $this->assertTrue($pack->isOk());

    $user = $user_service->getCurrentUser();
    $this->assertEquals(0, $user->status);

    $pack = $user_service->login($email, $password);
    $this->assertFalse($pack->isOk());
    $this->assertEquals('not-active', $pack->getError('user'));

    $this->_clearUser($user_service, $email, $nick);
}
```

