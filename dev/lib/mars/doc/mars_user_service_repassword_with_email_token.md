---
title: ::repasswordWithEmailToken
parentTitle: Mars\Service\UserService


## ::repasswordWithEmailToken

```php
\Mars\Service\UserService::repasswordWithEmailToken(\Mars\Dto\UserDto $user, string $new_password,
    string $encoded_email, string $token)
```

### parameters
- \Mars\Dto\UserDto $user
- string $new_password
- string $encoded_email
- string $token

### return
- \Mars\ParkDto $pack

### PHPUnit
```php
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

    $pack = $user_service->activateUserByEmail($email);
    $this->assertTrue($pack->isOk());

    $pack = $user_service->login($email, $password);
    $this->assertTrue($pack->isOk());
    $user = $pack->getItem('user');

    $pack = $user_service->repasswordWithEmailToken($user, $new_password, $encoded_email, $token);
    $this->assertTrue($pack->isOk());

    $pack = $user_service->login($email, $new_password);
    $this->assertTrue($pack->isOk());
}
```
