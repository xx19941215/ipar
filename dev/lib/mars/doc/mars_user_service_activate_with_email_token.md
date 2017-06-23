---
title: ::activateWithEmailToken
parentTitle: Mars\Service\UserService

## ::activateWithEmailToken

```php
\Mars\Service\UserService::activateWithEmailToken(\Mars\Dto\UserDto $user, string $encoded_email, $token)
```

### parameters
- \Mars\Dto\UserDto $user
- string $encoded_email
- string $token

### return
- \Mars\ParkDto $pack

### phpunit
```php
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

```
