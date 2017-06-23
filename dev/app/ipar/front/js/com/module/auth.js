define([
    'z.config'
], function(config) {
    'use strict';
    var auth = {};
    var requireLogin = function () {
        if(window.config.current_uid == 0){
            location.href = location.protocol +
                "//login." + config.base_host + '/' +
                window.config.locale_key + '/' +
                '?target=' + window.location.href;
            return false;
        }

        return true;
    }

    auth.requireLogin = requireLogin;
    return auth;
});
