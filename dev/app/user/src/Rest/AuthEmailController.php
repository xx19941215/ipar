<?php
namespace User\Rest;

class AuthEmailController extends \Gap\Routing\Controller
{

    protected $mailer;

    public function send()
    {
        $email = $this->request->request->get('email');
        $user = $this->service('user')->getUserByEmail($email);
        $effective_time = intval(config()->get('sendcloud.effective_time')) * 60;
        $email_code = $this->createEmailCode($email, $effective_time);
        $result = $this->sendMailCode($user, $email, $email_code);

        if ($result['message'] == 'success') {
            return $this->packOk();
        }
        return $this->packError('verification code', 'send fail');
    }

    public function EmailValidateCheck()
    {
        $email = $this->request->request->get('email');
        $code = $this->request->request->get('code');
        $uid = $this->request->request->get('uid');
        return $validateCode = $this->service('user')->isCorrectValidateEmailCode($email, $code, $uid);
    }

    public function createEmailCode($email, $effective_time)
    {
        $code = $this->service('user')->createEmailCode($email, $effective_time);

        return $code;
    }

    protected function sendMailCode($user, $email, $email_code)
    {
        return $this->getMailer()->send([
            'api_url' => 'send_template',
            'template_invoke_name' => 'sendEmailCode',
            'fromname' => 'no-reply',
            'to' => $email,
            'subject' => trans('validate-email'),
            'substitution_vars' => json_encode([
                'to' => [$email],
                'sub' => [
                    '%name%' => [$user->nick],
                    '%code%' => [$email_code],
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

}
