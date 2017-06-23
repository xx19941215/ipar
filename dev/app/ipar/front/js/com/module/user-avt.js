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
        $change_avt = s('.change-avt');

    $container.addClass('avt-editor');

    if ($change_avt.length > 0) {
        avt_panel = panel()
        require.ensure([], function() {
            avt_editor = require('zjs/z.imgeditor')({
                //upload_action: 'javascript:;',
                upload_action: z.config('api').user_upload_avt,
                width: 280,
                height: 280,
                pop_panel: avt_panel,
            }).done(function(avt_url) {
                s('.i-home-top .user-avt').html('<img src="' + avt_url + '">');
            });
            //avt_panel.show();
            //avt_editor.show();
        });

        $change_avt.on('click', function () {
            avt_editor.show();
            //avt_panel.show();
        });
    }

});

