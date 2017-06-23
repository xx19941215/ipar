<?php
namespace Mars\Service;

class UserService extends \Gap\Service\ServiceBase
{

    protected $current_user = null;
    protected $current_uid = null;
    protected $validator = null;

    protected $user_repo = null;

    public function bootstrap()
    {
        $this->validator = new \Mars\Validator\UserValidator();
        $this->user_repo = gap_repo_manager()->make('user');
    }

    public function getCurrentUid()
    {
        if ($this->current_uid) {
            return $this->current_uid;
        }
        //$this->current_uid = (int) session()->get('uid');
        return $this->current_uid;
    }

    public function getCurrentUser()
    {
        if ($this->current_user) {
            return $this->current_user;
        }
        if ($current_uid = current_uid()) {
            $this->current_user = $this->getUserByUid($current_uid);
            return $this->current_user;
        }
        return null;
    }

    public function setCurrentUid($uid)
    {
        $this->current_uid = $uid;
        $this->current_user = null;
        //session()->set('uid', $uid);
        return $this;
    }

    public function activateUser($query = [])
    {
        $pack = $this->user_repo->activateUser($query);
        if ($pack->isOk()) {
            $this->deleteCachedUser($pack->getItem('uid'));
        }
        return $pack;
    }

    public function deactivateUser($query = [])
    {
        $pack = $this->user_repo->deactivateUser($query);
        if ($pack->isOk()) {
            $this->deleteCachedUser($pack->getItem('uid'));
        }
        return $pack;
    }

    //
    // only deactivated user can be deleted
    //
    public function deleteUserByUid($uid)
    {
        $uid = (int)$uid;
        if (!$uid) {
            return $this->packError('uid', 'must-be-positive-integer');
        }
        $pack = $this->user_repo->deleteUserByUid($uid);
        if ($pack->isOk()) {
            $this->deleteCachedUser($uid);
        }
        return $pack;
    }

    public function deleteUserByEmail($email)
    {
        $pack = $this->user_repo->deleteUserByEmail($email);
        if ($pack->isOk()) {
            $this->deleteCachedUser($pack->getItem('uid'));
        }
        return $pack;
    }


    public function assignPrivilege($uid, $privilege)
    {
        $uid = (int)$uid;
        if (!$uid) {
            return $this->packError('uid', 'must-be-positive-integer');
        }
        $this->deleteCachedUser($uid);
        return $this->user_repo->assignPrivilege($uid, $privilege);
    }

    public function updateAvt($avt)
    {
        //$avt_json = json_encode($avt);
        $pack = $this->user_repo->updateAvt($avt);
        $this->deleteCachedUser($pack->getItem('uid'));
        return $pack;
    }

    public function switchUserByEmail($email)
    {
        if (!is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->packNotEmail('email');
        }
        $user = $this->getUserByEmail($email);
        if (!$user) {
            return $this->packNotFound('email');
        }
        if ($user->status != 1) {
            return $this->packError('user', 'not-active');
        }
        $this->setCurrentUid($user->uid);
        return $this->packItem('user', $user);
    }

    //
    // sc-get
    //

    public function schUserSet($opts = [])
    {
        return $this->user_repo->search($opts);
    }


    public function getUserByUid($uid)
    {
        $uid = (int)$uid;
        if (!$uid) {
            return null;
        }
        $key = "user-$uid";
        if ($cached = $this->cache()->get($key)) {
            return dto_decode($cached, 'user');
        }

        $user = $this->user_repo->getUserByUid($uid);
        //$this->cache()->set($key, dto_encode($user));
        return $user;
    }

    protected function deleteCachedUser($uid)
    {
        $this->cache()->delete("user-$uid");
    }

    public function getUserByZcode($zcode)
    {
        return $this->user_repo->getUserByZcode($zcode);
    }

    public function getUserByEmail($email)
    {
        $uid = $this->user_repo->findUid(['email' => $email]);
        return $this->getUserByUid($uid);
    }

    public function findUid($query)
    {
        return $this->user_repo->findUid($query);
    }

    //
    // for UserAuth
    //
    public function reg($email, $password, $nick)
    {
        $email = trim($email);
        $nick = trim($nick);

        if (true !== ($validated = $this->validator->validateEmail($email))) {
            return $validated;
        }
        if (true !== ($validated = $this->validator->validatePassword($password))) {
            return $validated;
        }
        if (true !== ($validated = $this->validator->validateNick($nick))) {
            return $validated;
        }

        $user_repo = $this->user_repo;

        if ($user_repo->findUid(['nick' => $nick])) {
            return $this->packExists('nick');
        }

        if ($user_repo->findUid(['email' => $email])) {
            return $this->packExists('email');
        }

        $passhash = password_hash($password, PASSWORD_DEFAULT);
        $pack = $user_repo->createUser($email, $passhash, $nick);

        if ($pack->isOk()) {
            $user = $this->getUserByEmail($email);
            $pack->addItem('user', $user);
        }
        return $pack;
    }

    public function login($email, $password)
    {
        if (true !== ($validated = $this->validator->validatePassword($password))) {
            return $validated;
        }

        $user = $this->getUserByEmail($email);

        if (!$user) {
            $user = $this->getUserByPhoneNumber($email);
        }

        if (!$user || isset($user->ok)) {
            return $this->packError('account', 'account-not-match');
        }

        if (!password_verify($password, $user->passhash)) {
            return $this->packError('password', 'password-not-match');
        }

        if ($user->status != 1) {
            return $this->packError('account', 'account-not-active');
        }


        $this->setCurrentUid($user->uid);
        $this->user_repo->logined($user->uid);
        $this->deleteCachedUser($user->uid);

        return $this->packItem('user', $user);
    }

    public function activateWithEmailToken($user, $encoded_email, $token)
    {
        if (!$this->verifyEmail($user, $encoded_email)) {
            return $this->packError('email', 'not-match');
        }
        if (!$this->verifyToken($user, $token)) {
            return $this->packError('token', 'not-match');
        }
        return $this->activateUser(['uid' => $user->uid]);
    }

    public function repasswordByUid($uid, $new_password)
    {
        if (true !== ($validated = $this->validator->validatePassword($new_password))) {
            return $validated;
        }
        $passhash = password_hash($new_password, PASSWORD_DEFAULT);
        $pack = $this->user_repo->repasshashByUid($uid, $passhash);
        if ($pack->isOk()) {
            $this->deleteCachedUser($uid);
        }
        return $pack;
    }

    public function repasswordWithEmailToken($user, $new_password, $encoded_email, $token)
    {
        if ($user->status != 1) {
            return $this->packError('user', 'not-active');
        }
        if (!$this->verifyEmail($user, $encoded_email)) {
            return $this->packError('email', 'not-match');
        }
        if (!$this->verifyToken($user, $token)) {
            return $this->packError('token', 'not-match');
        }
        return $this->repasswordByUid($user->uid, $new_password);
    }

    public function generateToken($user)
    {
        $key = substr($user->passhash, 15) . 'IdeaPar150707';
        return urlencode(substr(password_hash($key, PASSWORD_DEFAULT), 7));
    }

    public function encodeEmail($user)
    {
        $email = $user->getPrimaryEmail();
        return urlencode(md5($email . '-ipar'));
    }

    public function verifyToken($user, $token)
    {
        $token = '$2y$10$' . urldecode($token);
        $key = substr($user->passhash, 15) . 'IdeaPar150707';
        return password_verify($key, $token);
    }

    public function verifyEmail($user, $email_md5)
    {
        return (md5($user->getPrimaryEmail() . '-ipar') === urldecode($email_md5));
    }

    public function deleteUserByNick($nick)
    {
        $pack = $this->user_repo->deleteUserByNick($nick);
        if ($pack->isOk()) {
            $this->deleteCachedUser($pack->getItem('uid'));
        }
        return $pack;
    }

    /*
    public function cancelRole($uid) {
        $uid = (int) $uid;
        if (!$uid) {
            return $this->packError('uid', 'must-be-positive-integer');
        }
        $this->deleteCachedUser($uid);
        return $this->repo('user')->cancelRole($uid);
    }

    public function assignRole($uid, $role_id)
    {
        $uid = (int) $uid;
        if (!$uid) {
            return $this->packError('uid', 'must-be-positive-integer');
        }
        $this->deleteCachedUser($uid);
        return $this->repo('user')->assignRole($uid, $role_id);
    }

     */

    public function createWxUser($nick)
    {

        $nick = trim($nick);

        if ($this->user_repo->findUid(['nick' => $nick])) {
            return $this->packExists('nick');
        }

        $pack = $this->user_repo->createWxUser($nick);

        return $pack;
    }

    public function createWbUser($nick)
    {

        $nick = trim($nick);

        if ($this->user_repo->findUid(['nick' => $nick])) {
            return $this->packExists('nick');
        }

        $pack = $this->user_repo->createWbUser($nick);

        return $pack;
    }

    public function getUserByPhoneNumber($phone_number)
    {
        if (true !== ($validated = $this->validator->validatePhoneNumber($phone_number))) {
            return $validated;
        }

        return $this->user_repo->getUserByPhoneNumber($phone_number);
    }

    public function createMobileUser($phone_number, $password, $nick, $code)
    {
        $nick = trim($nick);

        if (true !== ($validated = $this->validator->validateNick($nick))) {
            return $validated;
        }

        if (true !== ($validated = $this->validator->validatePhoneNumber($phone_number))) {
            return $validated;
        }

        if (true !== ($validated = $this->validator->validatePassword($password))) {
            return $validated;
        }

        if (true !== ($validated = $this->validator->validateSMSCode($code))) {
            return $validated;
        }

        $user_repo = $this->user_repo;
        if ($this->user_repo->findUid(['nick' => $nick])) {
            return $this->packExists('nick');
        }

        if ($this->getUserByPhoneNumber($phone_number)) {
            return $this->packExists('phone_number');
        }

        if ($code !== $this->cache()->get($phone_number . 'sms_code')) {
            return $this->packError('verification-code', 'sms-code-not-match');
        }

        $passhash = password_hash($password, PASSWORD_DEFAULT);
        $pack = $user_repo->createMobileUser($phone_number, $passhash, $nick);

        if ($pack->isOk()) {
            $user = $this->getUserByPhoneNumber($phone_number);
            $pack->addItem('user', $user);
        }

        return $pack;
    }

    public function mobileLogin($phone_number, $password)
    {
        if (true !== ($validated = $this->validator->validatePassword($password))) {
            return $validated;
        }

        $user = $this->getUserByPhoneNumber($phone_number);

        if (!$user) {
            return $this->packError('account', 'account-not-match');
        }

        if (!password_verify($password, $user->passhash)) {
            return $this->packError('password', 'password-not-match');
        }

        if ($user->status != 1) {
            return $this->packError('account', 'account-not-active');
        }

        $this->setCurrentUid($user->uid);
        $this->user_repo->logined($user->uid);
        $this->deleteCachedUser($user->uid);

        return $this->packItem('user', $user);
    }

    public function createSMSCode($phone_number, $effective_time)
    {
        $code = mt_rand(100000, 999999);
        $this->cache()->set($phone_number . 'sms_code', $code, $effective_time);

        return $code;
    }

    public function createEmailCode($email, $effective_time)
    {
        $code = mt_rand(100000, 999999);
        $this->cache()->set($email . '_code', $code, $effective_time);

        return $code;
    }

    public function isCorrectSMSCode($phone_number, $code)
    {
        if ($code !== $this->cache()->get($phone_number . 'sms_code')) {
            return $this->packError('verification-code', 'sms-code-not-match');
        }
        return $this->packOk();
    }

    public function isCorrectValidateSMSCode($phone_number, $code, $uid)
    {
        if ($code !== $this->cache()->get($phone_number . 'sms_code')) {
            return $this->packError('verification-code', 'sms-code-not-match');
        }
        session()->set($uid . '_is_secondary_valid', 'true');
        return $this->packOk();
    }

    public function isCorrectValidateEmailCode($email, $code, $uid)
    {
        if ($code !== $this->cache()->get($email . '_code')) {
            return $this->packError('verification-code', 'email-sms-code-not-match');
        }
        session()->set($uid . '_is_secondary_valid', 'true');
        return $this->packOk();
    }

    public function getUserByNick($nick)
    {
        return $this->user_repo->getUserByNick($nick);
    }

    public function bindPhoneNumber($uid, $phone_number, $code)
    {
        if (true !== ($validated = $this->validator->validatePhoneNumber($phone_number))) {
            return $validated;
        }

        if (true !== ($validated = $this->validator->validateSMSCode($code))) {
            return $validated;
        }

        if ($this->getUserByPhoneNumber($phone_number)) {
            return $this->packExists('phone_number');
        }

        if ($code !== $this->cache()->get($phone_number . 'sms_code')) {
            return $this->packError('verification-code', 'sms-code-not-match');
        }
        return $this->user_repo->bindPhoneNumber($uid, $phone_number);
    }

    public function unbindWxFromUser($user)
    {
        return $this->user_repo->unbindWxFromUser($user);
    }

}
