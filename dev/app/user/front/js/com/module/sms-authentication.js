define([
    "zjs/z",
    "zjs/z.selector",
    "zjs/z.net"
], function (z, s, net) {
    "use strict";

    var $nick = s('input[name = "nick"]'),
        $verification_code = s('input[name = "code"]'),
        $password = s('input[name = "password"]'),
        $reg_phone_num = s('input[data-type = "reg-phone-number"]'),
        $reged_phone_num = s('input[data-type = "reged-phone-number"]'),
        $get_code = s('.get-code'),
        $get_reg_code = s('.get-reg-code'),
        $get_retrieve_code = s('.get-retrieve-code'),
        $mobile_reg_submit = s('.mobile-reg-submit'),
        $mobile_retrieve_password_submit = s('.mobile-retrieve-password-submit'),
        $view_password = s('.view-password'),
        $nick_error_tips = s('.nick-error-tips'),
        $phone_error_tips = s('.phone-error-tips'),
        $sms_error_tips = s('.sms-error-tips'),
        $password_error_tips = s('.password-error-tips'),
        $error_tips = s('.error-tips'),
        nick_error_type = 'empty',
        phone_error_type = 'empty',
        sms_error_type = 'empty',
        password_error_type = 'empty';

    $nick.on('input', function () {
        checkAvailableNickname();
    });

    $nick.on('focus', function () {
        nick_error_type == 'empty' && $nick_error_tips.html('');
        $nick_error_tips.addClass('hide');
    });

    $nick.on('blur', function () {
        nick_error_type == 'empty' && $nick_error_tips.html('');
        $nick_error_tips.removeClass('hide');
    });


    function checkAvailableNickname() {
        var nick = $nick.prop('value'),
            nickMaxLen = 21;

        if (!nick) {
            nick_error_type = 'empty';
            $nick_error_tips.html(z.trans('nick-empty'));
            return false;
        }

        if (nick.length > ++nickMaxLen) {
            nick_error_type = 'length';
            $nick_error_tips.html(z.trans('nick-length-less-than-%d').replace('%d', ++nickMaxLen));
            return false;
        }

        var isValid = /^[a-zA-Z\u4e00-\u9fa5][a-zA-Z0-9\u4e00-\u9fa5._-]*$/;
        if (!isValid.test(nick)) {
            nick_error_type = 'format';
            $nick_error_tips.html(z.trans('nick-format-error'));
            return false;
        }

        var fd = new FormData();
        fd.append('nick', nick);
        net.postPack('user-rest-nick-check', fd, function (pack) {
            if (pack.ok) {
                nick_error_type = '';
                $nick_error_tips.html('');
                return false;
            }
            nick_error_type = 'registered';
            $nick_error_tips.html('registered');
            return true;
        });
    }

    $reg_phone_num.on('input', function () {
        checkAvailablePhoneNumber();
    });

    $reg_phone_num.on('focus', function () {
        phone_error_type == 'empty' && $phone_error_tips.html('');
        $phone_error_tips.addClass('hide');
    });

    $reg_phone_num.on('blur', function () {
        phone_error_type == 'empty' && $phone_error_tips.html('');
        $phone_error_tips.removeClass('hide');
    });

    function checkAvailablePhoneNumber() {
        var phone_number = $reg_phone_num.prop('value');
        var phoneReg = /^\d{11}$/;

        if (!phone_number) {
            phone_error_type = 'empty';
            $phone_error_tips.html(z.trans('phone-empty'));
            return false;
        }

        if (!phoneReg.test(phone_number)) {
            $phone_error_tips.html(z.trans('phone-format-error'));
            phone_error_type = 'format';
            return false;
        }

        var fd = new FormData();
        fd.append('phone_number', phone_number);
        net.postPack('user-rest-phone-check', fd, function (pack) {
            if (pack.ok) {
                $phone_error_tips.html(z.trans('phone-number-used!'));
                phone_error_type = 'registered';
                return false;
            }
            $phone_error_tips.html('');
            phone_error_type = '';
            return true;
        });
    }

    $get_reg_code.on('click', function (e) {
        e.preventDefault();
        $get_code.attr('disabled', true);
        var phone_number = $reg_phone_num.prop('value');
        sendSMSCode(phone_number, 'register');
    });

    function sendSMSCode(phone_number, template_type) {
        if (!phone_number) {
            $phone_error_tips.html(z.trans('phone-empty'));
            $phone_error_tips.removeClass('hide');
            $get_code[0].disabled = false;
            return false;
        }

        if (phone_error_type) {
            $get_code[0].disabled = false;
            return false;
        }

        var fd = new FormData();
        fd.append('phone_number', phone_number);
        fd.append('template_type', template_type);
        net.postPack('user-rest-phone-send-code', fd, function (pack) {
            if (pack.ok) {
                var time = 60;
                var timer = setInterval(function () {
                    $get_code.html(z.trans('sent(%d-seconds)').replace('%d', time));
                    $get_code.addClass('code-sent');
                    time--;
                    if (time == 0) {
                        clearInterval(timer);
                        $get_code.html(z.trans('get-verification-code'));
                        $get_code[0].disabled = false;
                        $get_code.removeClass('code-sent');
                    }
                }, 1000);
                return true;
            }
            $sms_error_tips.html(z.trans('code-send-fail'));
            $sms_error_tips.removeClass('hide');
            $get_code[0].disabled = false;
            return false;
        });
    }

    $verification_code.on('input', function () {
        validateCode();
    });

    $verification_code.on('focus', function () {
        sms_error_type == 'empty' && $sms_error_tips.html('');
        $sms_error_tips.addClass('hide');
    });

    $verification_code.on('blur', function () {
        sms_error_type == 'empty' && $sms_error_tips.html('');
        $sms_error_tips.removeClass('hide');
    });

    function validateCode() {
        var code = $verification_code.prop('value'),
            SMSCodeLen = 6;

        if (!code) {
            $sms_error_tips.html(z.trans('code-empty'));
            sms_error_type = 'empty';
            return false;
        }

        if (code.length !== SMSCodeLen) {
            sms_error_type = 'length';
            $sms_error_tips.html(z.trans('code-error!'));
            return false;
        }
        $sms_error_tips.html('');
        sms_error_type = '';
        return true;
    }

    $password.on('input', function () {
        validatePassword();
    });

    $password.on('focus', function () {
        password_error_type == 'empty' && $password_error_tips.html('');
        $password_error_tips.addClass('hide');
    });

    $password.on('blur', function () {
        password_error_type == 'empty' && $password_error_tips.html('');
        $password_error_tips.removeClass('hide');
    });

    $view_password.on('click', function (e) {
        e.stopPropagation();
        if ($view_password.hasClass('icon-eye-close')) {
            $password[0].setAttribute('type', 'text');
            $view_password.removeClass('icon-eye-close');
            $view_password.addClass('icon-eye-open');
        } else {
            $password[0].setAttribute('type', 'password');
            $view_password.removeClass('icon-eye-open');
            $view_password.addClass('icon-eye-close');
        }
    });

    function validatePassword() {
        var password = $password.prop('value'),
            passwordMinLen = 6,
            passwordMaxLen = 21;

        if (!password) {
            password_error_type = 'empty';
            $password_error_tips.html(z.trans('password-empty'));
            return false;
        }

        if (password.length > passwordMaxLen || password.length < passwordMinLen) {
            $password_error_tips.html(z.trans('length-out-of-range'));
            password_error_type = 'length';
            return false;
        }

        $password_error_tips.html('');
        password_error_type = '';
        return true;
    }

    $mobile_reg_submit.on('click', function (e) {
        checkAvailableNickname();
        checkAvailablePhoneNumber();
        validateCode();
        validatePassword();
        if (nick_error_type || phone_error_type || sms_error_type || password_error_type) {
            $error_tips.removeClass('hide');
            e.preventDefault();
            return false;
        }
    });


    $get_retrieve_code.on('click', function (e) {
        e.preventDefault();
        $get_reg_code.attr('disabled', true);
        var phone_number = $reged_phone_num.prop('value');
        sendSMSCode(phone_number, 'validate');
    });

    $reged_phone_num.on('input', function () {
        checkRegisteredPhoneNumber();
    });

    $reged_phone_num.on('focus', function () {
        phone_error_type == 'empty' && $phone_error_tips.html('');
        $phone_error_tips.addClass('hide');
    });


    $reged_phone_num.on('blur', function () {
        phone_error_type == 'empty' && $phone_error_tips.html('');
        $phone_error_tips.removeClass('hide');
    });

    function checkRegisteredPhoneNumber() {
        var phone_number = $reged_phone_num.prop('value');
        var phoneReg = /^\d{11}$/;

        if (!phone_number) {
            $phone_error_tips.html(z.trans('phone-empty'));
            phone_error_type = 'empty';
            return false;
        }

        if (!phoneReg.test(phone_number)) {
            $phone_error_tips.html(z.trans('phone-format-error'));
            phone_error_type = 'format';
            return false;
        }

        var fd = new FormData();
        fd.append('phone_number', phone_number);
        net.postPack('user-rest-phone-check', fd, function (pack) {
            if (pack.ok) {
                $phone_error_tips.html('');
                phone_error_type = '';
                return true;
            }
            $phone_error_tips.html(z.trans('phone-number-not-registered!'));
            phone_error_type = 'not-registered';
            return false;

        });
    }

    $mobile_retrieve_password_submit.on('click', function (e) {
        checkRegisteredPhoneNumber();
        validateCode();
        validatePassword();
        if (phone_error_type || sms_error_type || password_error_type) {
            $error_tips.removeClass('hide');
            e.preventDefault();
            return false;
        }
    });

    window.untrans = function (str) {
        window.collector = window.collector || {};
        if (window.collector[str]) {
            return;
        }
        window.collector[str] = str;
        net.routePost('js_trans', {'key': str}, function (data) {
        });
    };
});


