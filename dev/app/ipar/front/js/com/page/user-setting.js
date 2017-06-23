define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'module/auth',
    'zjs/z.pop-panel'
], function (z, s, net, auth, panel) {
    'use strict';

    function userSettingValidateInit() {

        var p = panel(),
            ele = document.createElement('select');

        p.setPanelTitle(z.trans('Please validate'));
        appendForm(p, ele);
        var email = s('.user-email').find('.content')[0].dataset.email,
            phone = s('.user-phone').find('.content')[0].dataset.phone;

        if (email != '') {
            var email_value = email.trim(),
                email_text = z.trans('use-email') + ' ' + email_value + z.trans('to-validate');
            createOption(ele, email_value, email_text, true);
            createSendArea(ele, email_value, true);
        }

        if (phone != '') {
            var phone_value = phone.trim(),
                phoneText = z.trans('use-phone') + ' ' + phone_value + z.trans('to-validate');
            createOption(ele, phone_value, phoneText);
            createSendArea(ele, phone_value);
        }

        if (phone == '' && email == '') {
            bindTips();
            return;
        }

        var submit_btn = document.createElement('input');
        submit_btn.className = 'button';
        submit_btn.setAttribute('type', 'submit');
        submit_btn.innerHTML = z.trans('validate');

        ele.parentElement.append(submit_btn);
        s('.setting-wrap').get(0).addEventListener('click', validateBind, false);


        function sendPhoneCode(e) {
            var phone_number = e.toElement.parentNode.dataset.value,
                $get_code = s('.phone-send').find('button'),
                $sms_error_tips = s('.error_tips'),
                fd = new FormData();

            if (!phone_number) {
                $get_code[0].disabled = false;
                return false;
            }

            fd.append('phone_number', phone_number);
            fd.append('template_type', 'validate');
            net.postPack('user-rest-phone-send-code', fd, function (pack) {
                if (pack.ok) {
                    var time = 60;
                    var timer = setInterval(function () {
                        $get_code.html(z.trans('sent(%d-seconds)').replace('%d', time));
                        $get_code.addClass('code-sent');
                        time--;
                        if (time == 0) {
                            clearInterval(timer);
                            $get_code.html(z.trans('Send-code'));
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

        function sendEmailCode(e) {
            var email = e.toElement.parentNode.dataset.value,
                $get_code = s('.email-send').find('button'),
                $sms_error_tips = s('.error_tips'),
                fd = new FormData();
            fd.append('email', email);
            net.postPack('user-rest-email-send-code', fd, function (pack) {
                if (pack.ok) {
                    var time = 60;
                    var timer = setInterval(function () {
                        $get_code.html(z.trans('sent(%d-seconds)').replace('%d', time));
                        $get_code.addClass('code-sent');
                        time--;
                        if (time == 0) {
                            clearInterval(timer);
                            $get_code.html(z.trans('Send-code'));
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

        function createOption(parent_node, value, text, is_selected) {
            var option = document.createElement('option');
            option.value = value;
            if (is_selected) {
                option.setAttribute('selected', 'true');
            }
            option.innerHTML = text;
            parent_node.appendChild(option);
        }

        function selectValidateMethod(e) {
            var src = e.target || e.srcElement,
                value = src.value.trim(),
                patt = /^\d+$/,
                is_phone = value.match(patt);
            if (is_phone) {
                s('.phone-send').show();
                s('.email-send').hide();
            }
            else {
                s('.email-send').show();
                s('.phone-send').hide();
            }
        }

        function createSendArea(parent_node, value, is_selected) {
            var div = document.createElement('div'),
                patt = /^\d+$/;
            var is_phone = value.match(patt);
            if (is_phone) {
                div.className = 'phone-send';
                div.dataset.value = value;
            }
            else {
                div.className = 'email-send';
                div.dataset.value = value;
            }

            var input = document.createElement('input');
            input.type = 'text';
            input.setAttribute('placeholder', z.trans('Please-input-code'));
            input.style.width = '50%';
            input.style.display = 'inline-block';
            div.appendChild(input);
            var sms_error_tips = document.createElement('span');
            sms_error_tips.id = (is_phone ? 'phone_error_tips' : 'email_error_tips');
            sms_error_tips.style.display = 'none';
            div.insertBefore(sms_error_tips, input);

            var sendCode = document.createElement('button');
            sendCode.innerHTML = z.trans('Send-code');
            sendCode.className = 'button js-send-digits';
            div.appendChild(sendCode);
            parent_node.parentNode.appendChild(div);

            if (is_selected)
                div.style.display = 'block';
            else
                div.style.display = 'none';
        }


        function checkCode(e) {
            e.preventDefault();
            var form = this.parentElement,
                $pop = s('.pop-layout-wrapper'),
                select = form.getElementsByTagName('select')[0];
            var optionValue = select.options[select.selectedIndex].value;
            var patt = /^\d+$/;
            if (optionValue.match(patt)) {
                var phone = s('.phone-send').get(0).dataset.value;
                var code = s('.phone-send').find('input').prop('value');
                phoneCodeTrue(phone, code, $pop);
            } else {
                var email = s('.email-send').get(0).dataset.value;
                var code = s('.email-send').find('input').prop('value');
                emailCodeTrue(email, code, $pop);
            }
        }

        function phoneCodeTrue(phone, code, $pop) {
            if (!code)
                return false;
            var fd = new FormData(),
                span = s('#phone_error_tips')[0];
            fd.append('phone', phone);
            fd.append('code', code);
            fd.append('uid', config.current_uid);
            net.postPack('user-rest-phone-validate-check', fd, function (pack) {
                if (pack.ok) {
                    $pop.hide();
                    s('.setting-wrap').get(0).removeEventListener('click', validateBind, false);
                    window.sessionStorage.setItem('valid-' + config.current_uid, true);
                } else {
                    span.innerHTML = z.trans('code-is-invalid');
                    span.style.display = 'block';
                }
            })

        }


        function emailCodeTrue(email, code, $pop, spans) {
            if (!code)
                return false;
            var fd = new FormData(),
                span = s('#email_error_tips')[0];
            fd.append('email', email);
            fd.append('code', code);
            fd.append('uid', config.current_uid);
            net.postPack('user-rest-email-validate-check', fd, function (pack) {
                if (pack.ok) {
                    $pop.hide();
                    s('.setting-wrap').get(0).removeEventListener('click', validateBind, false);
                    window.sessionStorage.setItem('valid-' + config.current_uid, true);
                } else {
                    span.innerHTML = z.trans('code-is-invalid');
                    span.style.display = 'block';
                }
            })
        }

        function appendForm($parent_node, ele) {
            $parent_node.append($parent_node.html(z.trans('validate-hint')));

            var form = document.createElement('form');
            form.className = 'validate-form';
            form.appendChild(ele);

            $parent_node.append(form);
        }


        function validateBind(e) {
            p.is_hidding ? p.show() : p.hide();
            var src = e.target || e.srcElement;
            if (src.parentElement.parentElement.className == 'modify') {
                e.preventDefault();
                submit_btn.onclick = checkCode;
                s('.js-send-digits').on('click', function (e) {
                    if (this.parentElement.className == 'email-send') {
                        sendEmailCode(e);
                        s('.email-send').find('button')[0].disabled = 'true';
                    }
                    else {
                        sendPhoneCode(e);
                        s('.phone-send').find('button')[0].disabled = 'true';
                    }
                    e.preventDefault();

                });
                ele.onchange = selectValidateMethod;
                p.show();
            }
        }

        function bindTips() {
            var p = panel(),
                tipsContent = document.createElement('p');
            p.setPanelTitle(z.trans('Please-bind'));
            tipsContent.innerHTML = z.trans('bind-email-or-phone');
            p.append(tipsContent);
            s('.user-nick').find('a').get(0).addEventListener('click', function (e) {
                p.show();
                e.preventDefault();
            }, false);

            s('.user-password').find('a').get(0).addEventListener('click', function (e) {
                p.show();
                e.preventDefault();
            }, false);
        }
    }

    window.onload = function () {
        if (s('.ipar-ui-i-account-info').length > 0) {
            var validClass = "." + "valid-" + config.current_uid;
            if (s(validClass).length != 1)
                userSettingValidateInit();
        }

        if (s('.ipar-ui-i-third-party-account-binding').length > 0) {
            var thirdPartyValidClass = "." + "valid-" + config.current_uid;
            if (s(thirdPartyValidClass).length != 1)
                userSettingValidateInit();
        }

        if (s('.ipar-ui-i-change-nick').length > 0) {
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

            var $user_setting_change_nick_submit = s('.user-setting-change-nick-submit');
            $user_setting_change_nick_submit.on('click', function (e) {
                if (nick_error_type) {
                    $error_tips.removeClass('hide');
                    e.preventDefault();
                    return false;
                }
            })
        }
    };

    var $password = s('input[name = "password"]'),
        $view_password = s('.view-password');

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

    var $verification_code = s('input[name = "code"]'),
        $reg_phone_num = s('input[data-type = "reg-phone-number"]'),
        $get_code = s('.get-code'),
        $error_tips = s('.error-tips'),
        $get_reg_code = s('.get-reg-code'),
        $phone_error_tips = s('.phone-error-tips'),
        $sms_error_tips = s('.sms-error-tips'),
        phone_error_type = 'empty',
        sms_error_type = 'empty';

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

    var $nick = s("input[name='new-nick']"),

        nickMaxLen = 21,
        nick_error_type = 'empty',
        $nick_error_tips = s('.nick-error-tips');

    function checkAvailableNickname() {
        var nick = $nick.prop('value');
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

    var $user_setting_bind_phone_submit = s('.user-setting-bind-phone-submit');
    $user_setting_bind_phone_submit.on('click', function (e) {
        checkAvailablePhoneNumber();
        validateCode();
        if (phone_error_type || sms_error_type) {
            $error_tips.removeClass('hide');
            e.preventDefault();
            return false;
        }
    })

});