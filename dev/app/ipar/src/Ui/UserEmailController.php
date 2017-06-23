<?php
namespace Ipar\Ui;

class UserEmailController extends IparControllerBase
{
    protected $mailer;
    protected $user_service;

    public function bootstrap()
    {
        $this->user_service = user_service();
    }

    public function isCurrentUser($zcode)
    {
        if ($zcode != user(current_uid())->zcode) {
            return false;
        }
        return true;
    }

    public function change()
    {
        $user = $this->getUser();
        if($this->noEmailAndPhone($user))
            return $this->page('user/change-email', [
                'user' => $user
            ]);
        $isSecondaryValid = session()->get($user->uid . '_is_secondary_valid');
        if(!$isSecondaryValid)
            die($this->page('404/show'));
        return $this->page('user/change-email', [
            'user' => $user
        ]);
    }

    public function noEmailAndPhone($user)
    {
        if(!isset($user->email) && !isset($user->phone))
            return true;
        return false;
    }

    public function changePost()
    {
        $post = $this->getRequestData();
        $email = $post->get('new-email');
        $user = $this->getUser();
        $pack = $this->service('user_email')->verifyEmail($user, $email);

        if ($pack->ok) {
            $res = $this->sendResetMail($user, $email);
            if ($res['message'] == 'success') {
                return $this->gotoRoute('reset-email-success', [], ['domain' => $this->getDomainFromEmail($email)]);
            }
        }
        $data['user'] = $user;
        $data['errors'] = $pack->getErrors();
        return $this->page('user/change-email', $data);
    }

    public function resetEmailSuccess()
    {
        $domain = $this->request->query->get('domain');

        return $this->page('user/safe-success', [
            'tips' => trans('reset-email'),
            'title' => trans('activate-email-and-complete-reset-email'),
            'link_text' => trans('check-email-now'),
            'link_url' => $this->getMappingUrl($domain),
        ]);
    }

    public function resetEmail()
    {
        $query = $this->request->query;
        $date = (int)$query->get('date');
        if (time() - $date > 2592000) {
            die(trans('link-expired'));
        }

        $token = $query->get('token');
        $email = $query->get('email');
        $zcode = urldecode($query->get('zcode'));

        $user = $this->user_service->getUserByZcode($zcode);
        if (!$user) {
            die('error-request');
        }

        $res = $this->service('user_email')->changeEmailWithToken($user, $email, $token);
        if ($res) {
            return $this->gotoRoute('home');
        } else {
            die(trans('error-request'));
        }
    }

    public function getRequestData()
    {
        return $this->request->request;

    }

    public function getUser()
    {
        $zcode = $this->getParam('zcode');
        if (!$this->isCurrentUser($zcode)) {
            die($this->page('404/show'));
        }
        return $this->service('user_email')->getUserByZcode($zcode);
    }

    protected function sendResetMail($user, $email)
    {
        return $this->getMailer()->send([
            'api_url' => 'send_template',
            'template_invoke_name' => 'resetemail',
            'fromname' => 'no-reply',
            'to' => $email,
            'subject' => trans('reset-email'),
            'substitution_vars' => json_encode([
                'to' => [$email],
                'sub' => [
                    '%name%' => [$user->nick],
                    '%link%' => [
                        route_url(
                            'reset-email',
                            [],
                            [
                                'zcode' => urlencode($user->zcode),
                                'email' => $email,
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

    protected function getDomainFromEmail($email)
    {
        $pos = strpos($email, '@');
        $domain = substr($email, $pos + 1);

        return $domain;
    }

    protected function getMappingUrl($domain)
    {
        $mappings = [
            '163.com' => 'mail.163.com',
            'qq.com' => 'mail.qq.com',
            'yahoo.com' => 'mail.yahoo.com',
            'google.com' => 'mail.google.com',
            'vip.qq.com' => 'mail.qq.com'
        ];
        $domain = isset($mappings[$domain]) ? $mappings[$domain] : $domain;
        if (substr($domain, 0, 7) !== 'http://' || substr($domain, 0, 8) !== 'https://') {
            $url = '//' . $domain;
        } else {
            $url = $domain;
        }

        return $url;
    }
}
