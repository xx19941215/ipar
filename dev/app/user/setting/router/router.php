<?php
$this
    ->setCurrentAccess('public')

    ->setCurrentSite('login')
    ->get('/', ['as' => 'login', 'action' => 'User\Ui\UserController@login'])
    ->post('/', ['as' => 'login-post', 'action' => 'User\Ui\UserController@loginPost'])

    ->setCurrentSite('logout')
    ->get('/', ['as' => 'logout', 'action' => 'User\Ui\UserController@logout'])

    ->setCurrentSite('reg')
    ->get('/', ['as' => 'reg', 'action' => 'User\Ui\UserController@reg'])
    ->post('/', ['as' => 'reg-post', 'action' => 'User\Ui\UserController@regPost'])
    ->get('/success', ['as' => 'reg-success', 'action' => 'User\Ui\UserController@regSuccess'])
    ->get('/mobile', ['as' => 'mobile-reg', 'action' => 'User\Ui\AuthMobileController@reg'])
    ->post('/mobile', ['as' => 'mobile-reg-post', 'action' => 'User\Ui\AuthMobileController@regPost'])

    ->setCurrentSite('safe')
    ->get('/forgot-password', ['as' => 'forgot-password', 'action' => 'User\Ui\UserController@forgotPassword'])
    ->get('/mobile-forgot-password', ['as' => 'mobile-forgot-password', 'action' => 'User\Ui\AuthMobileController@mobileForgotPassword'])
    ->post('/mobile-forgot-password', [
        'as' => 'mobile-forgot-password-post',
        'action' => 'User\Ui\AuthMobileController@mobileForgotPasswordPost'
    ])
    ->get('/forgot-password-sent', ['as' => 'forgot-password-sent', 'action' => 'User\Ui\UserController@forgotPasswordSent'])
    ->post('/forgot-password', ['as' => 'forgot-password-post', 'action' => 'User\Ui\UserController@forgotPasswordPost'])

    ->get('/reset-password', ['as' => 'reset-password', 'action' => 'User\Ui\UserController@resetPassword'])
    ->get('/reset-password-success', ['as' => 'reset-password-success', 'action' => 'User\Ui\UserController@resetPasswordSuccess'])
    ->post('/reset-password', ['as' => 'reset-password-post', 'action' => 'User\Ui\UserController@resetPasswordPost'])
    ->get('/reset-email', ['as' => 'reset-email', 'action' => 'Ipar\Ui\UserEmailController@resetEmail'])
    ->get('/reset-email-success', ['as' => 'reset-email-success', 'action' => 'Ipar\Ui\UserEmailController@resetEmailSuccess'])
    ->get('/activate-user', ['as' => 'activate-user', 'action' => 'User\Ui\UserController@activateUser'])

    ->setCurrentSite('wx')
    ->get('/', ['as' => 'wx-login', 'action' => 'User\Ui\AuthWxController@login'])
    ->get('/bind-account', ['as' => 'wx-login-bind-account', 'action' => 'User\Ui\AuthWxController@bind'])
    ->post('/bind-account', ['as' => 'wx-login-bind-account-post', 'action' => 'User\Ui\AuthWxController@bindPost'])
    ->get('/reg', ['as' => 'wx-login-reg', 'action' => 'User\Ui\AuthWxController@reg'])
    ->get('/bind/', ['as' => 'bind-wx', 'action' => 'User\Ui\AuthWxController@bindWx'])

    ->setCurrentSite('wb')
    ->get('/', ['as' => 'wb-login', 'action' => 'User\Ui\AuthWbController@login'])
    ->get('/bind-suggest', ['as' => 'wb-login-suggest', 'action' => 'User\Ui\AuthWbController@loginsuggest'])
    ->get('/bind-account', ['as' => 'wb-login-bind-account', 'action' => 'User\Ui\AuthWbController@bind'])
    ->post('/bind-account', ['as' => 'wb-login-bind-account-post', 'action' => 'User\Ui\AuthWbController@bindPost'])
    ->get('/reg', ['as' => 'wb-login-reg', 'action' => 'User\Ui\AuthWbController@reg']);
