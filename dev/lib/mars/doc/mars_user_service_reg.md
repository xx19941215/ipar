---
title: ::reg
parentTitle: Mars\Service\UserService

## ::reg

```php
\Mars\Service\UserService::reg(string $email, string $password, string $nick)
```

### parameters

- string $email
- string $password
- string $nick; alphabets, numbers, '-', '_', '.' and chinese character,
    but first letter can only be alphabet and chinese character


### return

- \Mars\PackDto $pack
    - error
        - email: not-email
        - email: exists
        - password: empty
        - password: out-range, 3 - 21
        - nick: empty
        - nick: out-range, 1 - 21
        - nick: error-format
        - nick: exists
    - ok
        - user: \Mars\Dto\UserDto

### PHPUnit

```php
<?php
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
```
