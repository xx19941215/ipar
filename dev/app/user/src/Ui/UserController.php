<?php
/**
 * PHP Version 5.6.
 *
 * @category Ui
 *
 * @author Zjh <zhanjh@126.com>
 * @license http:://www.tecposter.cn/bsd-licence BSD Licence
 *
 * @link http:://www.tecposter.cn/
 **/

namespace User\Ui;

/**
 * An UserController Class for authentication.
 *
 * @category Ui
 *
 * @author Zjh <zhanjh@126.com>
 * @license http:://www.tecposter.cn/bsd-licence BSD Licence
 *
 * @link http:://www.tecposter.cn/
 **/
class UserController extends \Gap\Routing\Controller
{
    protected $mailer;
    protected $user_service;

    public function bootstrap()
    {
        $this->user_service = user_service();
    }

    /**
     * Reg view.
     *
     * @return view string
     *
     * @author Zjh <zhanjh@126.com>
     **/
    public function reg()
    {
        $session = session();
        $wx_state = md5(uniqid());
        $session->set('wx_state', $wx_state);
        $wb_state = md5(uniqid());
        $session->set('wb_state', $wb_state);

        return $this->page('user/reg', [
            'wx_state' => $wx_state,
            'wb_state' => $wb_state
        ]);
    }

    /**
     * Reg Post.
     *
     * @return view string | RedirectResponse
     *
     * @author Zjh <zhanjh@126.com>
     **/
    public function regPost()
    {
        $post = $this->request->request;
        $email = $post->get('email');
        $password = $post->get('password');
        $nick = $post->get('nick');

        $reg_pack = $this->user_service->reg($email, $password, $nick);

        if ($reg_pack->ok) {
            $new_user = $reg_pack->getItem('user');
            $rs = $this->sendActivateMail($new_user, $email);
            //$this->pushMessage(trans('user-reg-success'));
            return $this->gotoRoute('reg-success', [], ['domain' => $this->getDomainFromEmail($email)]);
        } else {
            $data = $post->all();
            $data['errors'] = $reg_pack->getErrors();

            return $this->page('user/reg', $data);
        }
    }

    public function activateUser()
    {
        $query = $this->request->query;
        $date = (int)$query->get('date');
        if (time() - $date > 2592000) {
            die(trans('link-expired'));
        }

        $token = $query->get('token');
        $encoded_email = $query->get('encoded_email');
        $zcode = urldecode($query->get('zcode'));

        $this->user_service = $this->user_service;
        $user = $this->user_service->getUserByZcode($zcode);
        if (!$user) {
            die('error-request');
        }

        $pack = $this->user_service->activateWithEmailToken($user, $encoded_email, $token);
        if ($pack->isOk()) {
            return $this->page('user/safe-success', [
                'tips' => trans('user-reg'),
                'title' => trans('email-activate-success'),
                'link_text' => trans('login-now'),
                'link_url' => route_url('login'),
            ]);
        } else {
            die(trans('error-request'));
        }
    }

    public function regSuccess()
    {
        $domain = $this->request->query->get('domain');

        return $this->page('user/safe-success', [
            'tips' => trans('user-reg'),
            'title' => trans('activate-email-and-complete-reg'),
            'link_text' => trans('check-email-now'),
            'link_url' => $this->getMappingUrl($domain),
        ]);
    }

    /**
     * Login.
     *
     * @return string or RedirectResponse
     *
     * @author Zjh <zhanjh@126.com>
     **/
    public function login()
    {
        $target = $this->request->query->get('target');
        if (current_uid()) {
            if ($target) {
                return $this->gotoUrl($target);
            } else {
                return $this->gotoRoute('home');
            }
        }
        $session = session();
        if ($target) {
            $this->setTargetUrl($target);
        }
        $wx_state = md5(uniqid());
        $session->set('wx_state', $wx_state);
        $wb_state = md5(uniqid());
        $session->set('wb_state', $wb_state);

        return $this->page(
            'user/login',
            [
                'target' => $target,
                'wx_state' => $wx_state,
                'wb_state' => $wb_state
            ]
        );
    }

    /**
     * Login authenticate post username & password.
     *
     * @return string | RedirectResponse
     *
     * @author Zjh <zhanjh@126.com>
     **/

    public function loginPost()
    {
        $account = $this->request->request->get('account');
        $password = $this->request->request->get('password');
        $hit = 0;

        if (!is_string($account) || empty($account)) {
            return $this->page(
                'user/login',
                [
                    'account' => $account,
                    'errors' => [trans('account') => trans('empty')],
                ]
            );
        }

        if (filter_var($account, FILTER_VALIDATE_EMAIL)) {
            $hit++;
            $login_pack = $this->user_service->login($account, $password);
        }

        if (preg_match('~^(\d{11})$~u', $account)) {
            $hit++;
            $login_pack = $this->user_service->mobileLogin($account, $password);
        }

        if ($hit == 0) {
            return $this->page(
                'user/login',
                [
                    'account' => $account,
                    'errors' => [trans('account') => trans('type-error')],
                ]
            );
        }

        if ($login_pack->isOk()) {
            set_current_uid($login_pack->getItem('user')->uid);
            return $this->gotoTargetUrl();
        } else {
            return $this->page(
                'user/login',
                [
                    'account' => $account,
                    'errors' => $login_pack->getErrors(),
                ]
            );
        }
    }

    /**
     * Logout.
     *
     * @return RedirectResponse
     *
     * @author Zjh <zhanjh@126.com>
     **/
    public function logout()
    {
        session()->clear();
        if ($target = $this->request->query->get('target')) {
            return $this->gotoUrl($target);
        } else {
            return $this->gotoRoute('home');
        }
    }

    /* -- todo -- */

    public function wxOauthCallback()
    {
        return 'success';
    }

    /**
     * Forgot Password.
     *
     * @return view string
     *
     * @author Zjh <zhanjh@126.com>
     **/
    public function forgotPassword()
    {
        return $this->page(
            'user/forgot-password',
            [
                'hide_nav' => true,
            ]
        );
    }

    public function forgotPasswordSent()
    {
        $domain = $this->request->query->get('domain');

        return $this->page('user/safe-success', [
            'tips' => trans('user-safe'),
            'title' => trans('reset-password-email-sent-success'),
            'link_text' => trans('check-email-now'),
            'link_url' => $this->getMappingUrl($domain),
        ]);
    }

    public function forgotPasswordPost()
    {
        $email = $this->request->request->get('email');
        $user = $this->user_service->getUserByEmail($email);
        if ($user) {
            $rs = $this->sendRepasswordMail($user, $email);
            if ($rs['message'] === 'success') {
                $pos = strpos($email, '@');
                $domain = substr($email, $pos + 1);

                return $this->gotoRoute('forgot-password-sent', [], ['domain' => $domain]);
            } else {
                return $this->page(
                    'user/forgot-password',
                    [
                        'errors' => ['email' => 'reset-password-email-sent-failed'],
                        'email' => $email,
                        'hide_nav' => true,
                    ]
                );
            }
        } else {
            return $this->page(
                'user/forgot-password',
                [
                    'errors' => ['email' => 'not-exists'],
                    'email' => $email,
                    'hide_nav' => true,
                ]
            );
        }
    }

    public function resetPassword()
    {
        if ($uid = current_uid()) {
            $data['uid'] = $uid;
        } else {
            $query = $this->request->query;

            $zcode = urldecode($query->get('zcode'));
            $encoded_email = $query->get('encoded_email');
            $token = $query->get('token');
            $date = (int) $query->get('date');

            if (time() - $date > 2592000) {
                die('link-expired' . ':1');
            }
            $user = $this->user_service->getUserByZcode($zcode);
            if (!$user) {
                die('error-request' . ':2');
            }
            $data = [
                'uid' => $user->uid,
                'token' => $token,
                'encoded_email' => $encoded_email,
                'date' => $date,
            ];
        }

        return $this->page(
            'user/reset-password',
            $data
        );
    }

    public function resetPasswordSuccess()
    {
        return $this->page('user/safe-success', [
            'tips' => trans('user-safe'),
            'title' => trans('user-reset-password-success'),
            'link_text' => trans('login-now'),
            'link_url' => route_url('login'),
        ]);
    }

    public function resetPasswordPost()
    {
        $post = $this->request->request;
        $email = $post->get('email');
        $password = $post->get('password');

        if (!$uid = current_uid()) {
            $encoded_email = $post->get('encoded_email');
            //$uid = $post->get('uid');
            $token = $post->get('token');
            $date = (int) $post->get('date');

            if (time() - $date > 2592000) {
                die(':link-expired');
            }
            $user = $this->user_service->getUserByEmail($email);
            if (!$user) {
                die('error-request' . ': 01');
            }
            if ($user->status != 1) {
                $pack = $this->user_service->activateWithEmailToken($user, $encoded_email, $token);
                if (!$pack->isOk()) {
                    die('error-request: 101');
                }
                $user = $pack->getItem('user');
            }
            $pack = $this->user_service->repasswordWithEmailToken($user, $password, $encoded_email, $token);
        } else {
            $user = $this->user_service->getUserByUid($uid);
            $pack = $this->user_service->repasswordByUid($uid, $password);
        }
        if ($pack->isOk()) {
            return $this->gotoRoute('reset-password-success');
        } else {
            return $this->page('user/reset-password', [
                'uid' => $uid,
                'token' => $token,
                'encoded_email' => $encoded_email,
                'date' => $date,
                'errors' => $pack->getErrors(),
            ]);
        }
    }

    protected function sendActivateMail($user, $email)
    {
        $rs = $this->getMailer()->send([
            'api_url' => 'send_template',
            'template_invoke_name' => 'active',
            'fromname' => 'no-reply',
            'to' => $email,
            'subject' => trans('mail-activate'),
            'substitution_vars' => json_encode([
                'to' => [$email],
                'sub' => [
                    '%name%' => [$user->nick],
                    '%link%' => [
                        route_url(
                            'activate-user',
                            [],
                            [
                                'zcode' => urlencode($user->zcode),
                                'encoded_email' => $this->encodeEmail($user),
                                'token' => $this->generateToken($user),
                                'date' => time(),
                            ]
                        ),
                    ],
                ],
            ]),
        ]);

        return $rs;
    }

    protected function sendRepasswordMail($user, $email)
    {
        return $this->getMailer()->send([
            'api_url' => 'send_template',
            'template_invoke_name' => 'repassword',
            'fromname' => 'no-reply',
            'to' => $email,
            'subject' => trans('reset-password'),
            'substitution_vars' => json_encode([
                'to' => [$email],
                'sub' => [
                    '%name%' => [$user->nick],
                    '%link%' => [
                        route_url(
                            'reset-password',
                            [],
                            [
                                'zcode' => urlencode($user->zcode),
                                'encoded_email' => $this->encodeEmail($user),
                                'token' => $this->generateToken($user),
                                'date' => time(),
                            ]
                        ),
                    ],
                ],
            ]),
        ]);
    }

    protected function getMailer()
    {
        if (!$this->mailer) {
            $this->mailer = new \Gap\Mail\Driver\SendCloud(
                config()->get('mail.sendcloud')->all()
            );
        }

        return $this->mailer;
    }

    protected function generateToken($user)
    {
        return $this->user_service->generateToken($user);
    }

    protected function encodeEmail($user)
    {
        return $this->user_service->encodeEmail($user);
    }

    protected function pushMessage($msg)
    {
        $this->request->getSession()->set('msg', $msg);
    }

    protected function pullMessage()
    {
        $session = $this->request->getSession();
        $msg = $session->get('msg');

        return $msg;
    }

    protected function getMappingUrl($domain)
    {
        $mappings = [
            '163.com' => 'mail.163.com',
            'qq.com' => 'mail.qq.com',
            'yahoo.com' => 'mail.yahoo.com',
            'google.com' => 'mail.google.com',
        ];
        $domain = isset($mappings[$domain]) ? $mappings[$domain] : $domain;
        if (substr($domain, 0, 7) !== 'http://' || substr($domain, 0, 8) !== 'https://') {
            $url = '//' . $domain;
        } else {
            $url = $domain;
        }

        return $url;
    }

    protected function loginedRedirect()
    {
        if ($target = session()->get('url_target')) {
            return $this->gotoUrl($target);
        } else {
            return $this->gotoRoute('home');
        }
    }

    protected function getDomainFromEmail($email)
    {
        $pos = strpos($email, '@');
        $domain = substr($email, $pos + 1);

        return $domain;
    }
}
