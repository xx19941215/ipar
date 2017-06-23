define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    "zjs/z.pop-panel",
    'zjs/z.simple-template-engine'
], function (z, s, net, panel, tpl) {
    'use strict';

    var Rqt;

    Rqt = function (opts) {
        opts = opts || {};

        this.panel = panel();

        this.panel.html(
            tpl(require('tpl/rqt-form.tpl'), {
                title: opts.title || '',
                content: opts.content || '',
                title_placeholder: opts.title_placeholder || z.trans('rqt-title'),
                content_placeholder: opts.content_placeholder || z.trans('rqt-content'),
                submit: z.trans('submit')
            })
        );

        this.init();
    };

    Rqt.prototype = {
        init: function () {
            var $panel = this.panel.$panel;
            require.ensure([], function() {
                require('zjs/z.editor')($panel.find('.zeditor'), ['image', 'bold', 'insertUnorderedList', 'justifyLeft', 'justifyCenter']).autoheight();
                $panel.find('.rqt-form').on('submit', function (e) {
                    z.cancelEvent(e);
                    net.routePost('rqt_save', new window.FormData(this), function (pack) {
                        if (pack.isOk()) {
                            window.location = '/';
                            return;
                        }
                        window.alert('save-failed');
                    });
                    return false;
                });
            });

        },
        show: function () {
            this.panel.show();
        }
    };

    return function(opts) {
        return new Rqt(opts);
    };
});
