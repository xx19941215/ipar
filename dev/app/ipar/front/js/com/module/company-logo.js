define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.pop-panel',
    //'zjs/z.simple-template-engine',
], function(z, s, net, panel) {
    'use strict';

    var $container = s.create('div'),
        avt_panel,
        avt_editor,
        $gid = s('.gid'),
        $change_avt = s('.change-company-logo');
        $container.addClass('avt-editor');

    if ($change_avt.length > 0) {
        avt_panel = panel()
        require.ensure([], function() {
            avt_editor = require('zjs/z.imgeditor')({
                upload_action: z.config('api').upload_logo,
                width: 280,
                height: 280,
                pop_panel: avt_panel,
            }).done(function(act_url) {
                s('.change-company-logo').html('<img src="' + act_url.logo_url + '">');
                if ($gid.length > 0) {
                    var gid = $gid[0].value;

                net.routePost('update_logo',
                    {gid: gid, logo: act_url.logo},
                    function (pack){
                        if (!pack.isOk()) {
                            console.log(pack);
                        }
                });
                } else {
                    s('.company-logo-url')[0].value = act_url.logo;
                }
            });
        });

        $change_avt.on('click', function () {
            avt_editor.show();
            //avt_panel.show();
        });
    }
});

