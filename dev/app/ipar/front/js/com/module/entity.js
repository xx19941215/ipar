define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    "zjs/z.pop-panel",
    'zjs/z.simple-template-engine'
], function (z, s, net, panel, tpl) {
    'use strict';

    var Entity;

    Entity = function (opts) {
        var type;
        opts = opts || {};

        type = opts.type || '';
        if (!type) {
            console.log('type cannot be empty');
            return;
        }
        this.type = type;

        this.panel = panel();

        this.panel.html(
            tpl(require('tpl/entity-form.tpl'), {
                title: opts.title || '',
                content: opts.content || '',
                title_placeholder: opts.title_placeholder || z.trans(type + '-title'),
                content_placeholder: opts.content_placeholder || z.trans(type + '-content'),
                submit: z.trans('submit')
            })
        );

        this.init();
    };

    Entity.prototype = {
        init: function () {
            var $panel = this.panel.$panel,
                self = this;
            require.ensure([], function() {
                self.zeditor = require('zjs/z.editor')($panel.find('.zeditor'), ['image', 'bold', 'insertUnorderedList', 'justifyLeft', 'justifyCenter']);
                self.zeditor.autoheight();

                $panel.find('.entity-form').on('submit', function (e) {
                    z.cancelEvent(e);
                    net.routePost(self.type + '_save', new window.FormData(this), function (pack) {
                        if (pack.isOk()) {
                            //window.location = '/';
                            window.location = window.location;
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
            return this;
        },
        setTitle: function (title) {
            this.panel.$panel.find('input[name="title"]').prop('value', title);
            return this;
        },
        setContent: function (content) {
            if (this.zeditor) {
                this.zeditor.val(content);
                return this;
            }

            this.panel.$panel.find('.zeditor').html(content);
            return this;
        },
        setEid: function (eid) {
            this.panel.$panel.find('input[name="eid"]').prop('value', eid);
            return this;
        }
    };

    return function(opts) {
        return new Entity(opts);
    };
});
