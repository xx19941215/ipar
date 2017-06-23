define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
], function(z, s, net, pop_panel) {
    'use strict';

    var form_util = {
        setZeditorContent: function (zeditor, $ctn, content) {
            content = content.trim();

            if (zeditor) {
                zeditor.val(content);
                content = content;
                return;
            }

            if (!content) {
                content = "<p class='empty'>&zwj;</p>";
            }

            $ctn.find('.zeditor').html(content);
        }
    };

    return form_util;
});
