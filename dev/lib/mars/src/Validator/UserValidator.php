<?php
namespace Mars\Validator;

class UserValidator extends \Gap\Validator\ValidatorBase
{
    public function validatePassword($password)
    {
        if (!is_string($password) || empty($password)) {
            return $this->packEmpty('password');
        }
        $password_length = mb_strlen($password);
        if ($password_length < 3) {
            return $this->packError('password', ['minlength-%d', 3]);
            //return $this->packOutRange('password', 3, 21);
        }
        return true;
    }

    public function validateNick($nick)
    {
        if (!is_string($nick) || empty($nick)) {
            return $this->packEmpty('nick');
        }

        $nick_length = mb_strlen($nick);
        if ($nick_length < 1 || $nick_length > 21) {
            return $this->packOutRange('nick', 1, 21);
        }
        if (!preg_match(
            '~^[a-zA-Z\x{4e00}-\x{9fa5}][a-zA-Z0-9\x{4e00}-\x{9fa5}._-]+$~u',
            $nick
        )
        ) {
            return $this->packError('nick', 'error-format');
        }
        return true;
    }

    public function validateEmail($email)
    {
        if (empty($email)) {
            return $this->packEmpty('email');
        }
        if (!is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->packNotEmail('email');
        }

        return true;
    }

    public function validatePhoneNumber($phone_number)
    {
        if (!is_string($phone_number) || empty($phone_number)) {
            return $this->packEmpty('phone number');
        }

        if (!preg_match(
            '~^(\d{11})$~u',
            $phone_number
        )
        ) {
            return $this->packError('phone_number', 'error-format');
        }

        return true;
    }

    public function validateSMSCode($code)
    {
        if (!is_string($code) || empty($code)) {
            return $this->packEmpty('verification-code');
        }

        if (!preg_match(
            '~^(\d{6}$)~u',
            $code
        )
        ) {
            return $this->packError('verification-code','error-format');
        }

        return true;
    }
}
