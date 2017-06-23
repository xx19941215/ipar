define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    "zjs/z.pop-panel",
    'zjs/z.simple-template-engine'
], function (z, s, net, panel, tpl) {
    'use strict';

    var Idea;

    Idea = function (opts) {
        opts = opts || {};

        this.panel = panel({panel_class: 'form-wrap'});

        this.panel.html(
            tpl(require('tpl/idea-form.tpl'), {
                eid: opts.eid || '',
                rqt_eid: opts.rqt_eid || '',
                content: opts.content || '',
                content_placeholder: opts.content_placeholder || z.trans('idea-content'),
                submit: z.trans('submit'),
                panel_title: z.trans('solution-idea')
            })
        );

        this.init();
    };

    Idea.prototype = {
        init: function () {
            var $panel = this.panel.$panel;
            require.ensure([], function() {
                require('zjs/z.editor')($panel.find('.zeditor'), ['image', 'bold', 'insertUnorderedList', 'justifyLeft', 'justifyCenter']).autoheight();
                $panel.find('.idea-form').on('submit', function (e) {
                    z.cancelEvent(e);
                    net.routePost('rqt_save_idea', new window.FormData(this), function (pack) {
                        if (pack.isOk()) {
                            window.location = window.location;
                            return;
                        }
                        window.alert('save-failed');
                    });
                    return false;
                });
            });

        },
        setRqtEid: function (rqt_eid) {
            this.panel.$panel.find('input[name="rqt_eid"]').prop('value', parseInt(rqt_eid));
            return this;
        },
        show: function () {
            this.panel.show();
            return this;
        }
    };

    return function(opts) {
        return new Idea(opts);
    };
});
