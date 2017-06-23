---
title: Mars\Service\UserService
parentTitle: Mars


# Mars\Service\UserService

- file: src/lib/mars/src/Service/UserService.php
- class: Mars\Service\UserService
- phpunit: src/lib/mars/test/Unit/UserTest.php

**functions return PackDto**
- [::reg](#mars_user_service_reg)
- [::login](#mars_user_service_login)
- [::changePassword](#mars_user_service_change_password)
- [::activateUserByUid](#mars_user_service_activate_user_by_uid)
- [::activateUserByEmail](#mars_user_service_activate_user_by_email)
- [::activateUserWithToken](#mars_user_service_activate_user_with_token)
- [::deactivateUserByUid](#mars_user_service_deactivate_user_by_uid)
- [::deleteUserByUid](#mars_user_service_delete_user_by_uid)
- [::deleteUserByEmail](#mars_user_service_delete_user_by_email)
- [::deleteUserByNick](#mars_user_service_delete_user_by_nick)
- [::switchUserByEmail](#mars_user_service_switch_user_by_email)
- [::updateAvt](#mars_user_service_update_avt)

**functions**
- [::getCurrentUid](#mars_user_service_get_currentuid)
- [::getCurrentUser](#mars_user_service_get_current_user)

**functions return $this**
- [::setCurrentUid](#mars_user_service_set_current_uid)
