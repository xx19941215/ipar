<?php
namespace User\Test\Cmd;

class UserCmd
{
    public function regTesters()
    {
        $user_service = service('user');
        foreach (config()->get('tester.available')->all() as $tester) {
            $email = prop($tester, 'email');
            $password = prop($tester, 'password');
            $nick = prop($tester, 'nick');

            $pack = $user_service->reg($email, $password, $nick);

            if ($pack->isOk()) {
                add_log("[$email, $nick] reg success");

                $new_user = $pack->getItem('user');
                //$activate_pack = $user_service->activateEmail($new_user->uid, $email);
                $activate_pack = $user_service->activateUser(['uid' => $new_user->uid]);

                if ($activate_pack->isOk()) {
                    add_log("[$email, $nick] activate success");
                } else {
                    add_log("[$email, $nick] activate failed:");
                    add_log($activate_pack->getErrors());
                }

            } else {
                add_log("[$email, $nick] reg failed:");
                add_log($pack->getErrors());
            }

            /*
            $login_pack = $user_service->login($email, $password);
            if ($login_pack->isOk()) {
                add_log("[$email, $nick] login success");
            } else {
                add_log("[$email, $nick] login failed");
                add_log($login_pack->getErrors());
            }

            $current_user = $user_service->getCurrentUser();
            $primary_email = $current_user->getPrimaryEmail();

            if ($current_user->nick !== $nick || $primary_email !== $email) {
                add_log("[$email, $nick] does not match current_user [$primary_email, {$current_user->nick}]");
            }
            */

        }
        echo_logs();
    }

}
