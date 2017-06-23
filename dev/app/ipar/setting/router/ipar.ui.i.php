<?php
$this->setCurrentSite('i')
    ->setCurrentAccess('public')

    ->get('/{zcode:[0-9a-z-]+}/account-info', ['as' => 'ipar-ui-i-account-info',
        'action' => 'Ipar\Ui\UserSettingController@accountInfo'])
    ->get('/{zcode:[0-9a-z-]+}/third-party-account-binding', ['as' => 'ipar-ui-i-third-party-account-binding',
        'action' => 'Ipar\Ui\UserSettingController@thirdPartyAccountBinding'])
    ->get('/{zcode:[0-9a-z-]+}/profile', ['as' => 'ipar-ui-i-profile',
        'action' => 'Ipar\Ui\UserSettingController@profile'])
    ->post('/{zcode:[0-9a-z-]+}/profile', ['as' => 'ipar-ui-i-profile-post',
        'action' => 'Ipar\Ui\UserInfoController@save'])
    ->get('/{zcode:[0-9a-z-]+}/change-nick', [
        'as' => 'ipar-ui-i-change-nick',
        'action' => 'Ipar\Ui\UserNickController@change'
    ])
    ->post('/{zcode:[0-9a-z-]+}/change-nick', [
        'as' => 'ipar-ui-i-change-nick',
        'action' => 'Ipar\Ui\UserNickController@changePost'
    ])
    ->get('/{zcode:[0-9a-z-]+}/change-email', [
        'as' => 'ipar-ui-i-change-email',
        'action' => 'Ipar\Ui\UserEmailController@change'
    ])
    ->post('/{zcode:[0-9a-z-]+}/change-email', [
        'as' => 'ipar-ui-i-change-email',
        'action' => 'Ipar\Ui\UserEmailController@changePost'
    ])
    ->get('/{zcode:[0-9a-z-]+}/change-password',[
        'as' => 'ipar-ui-i-change-password',
        'action' => 'Ipar\Ui\UserPasswordController@change'
    ])
    ->get('/{zcode:[0-9a-z-]+}/set-password',[
        'as' => 'ipar-ui-i-set-password',
        'action' => 'Ipar\Ui\UserPasswordController@change'
    ])
    ->post('/{zcode:[0-9a-z-]+}/change-password',[
        'as' => 'ipar-ui-i-change-password-post',
        'action' => 'Ipar\Ui\UserPasswordController@changePost'
    ])
    ->post('/{zcode:[0-9a-z-]+}/set-password',[
        'as' => 'ipar-ui-i-set-password-post',
        'action' => 'Ipar\Ui\UserPasswordController@setPost'
    ])
    ->get('/{zcode:[0-9a-z-]+}/bind-phone', [
        'as' => 'ipar-ui-i-bind-phone',
        'action' => 'Ipar\Ui\UserPhoneController@bind'
    ])
    ->post('/{zcode:[0-9a-z-]+}/bind-phone', [
        'as' => 'ipar-ui-i-bind-phone-post',
        'action' => 'Ipar\Ui\UserPhoneController@bindPost'
    ])
    ->get('/{zcode:[0-9a-z-]+}/unbind-wx', [
        'as' => 'ipar-ui-i-unbind-wx',
        'action' => 'User\Ui\AuthWxController@unbindWxFromUser'
    ])
    ;