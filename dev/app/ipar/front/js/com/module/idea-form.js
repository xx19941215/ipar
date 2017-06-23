define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.simple-template-engine',
    'module/form-util'
], function (z, s, net, tpl, form_util) {
    'use strict';

    function IdeaForm(opts) {
        opts = opts || {};
        if (!opts.$ctn) {
            console.log('ctn cannot be empty');
            return;
        }

        this.$ctn = opts.$ctn;
        this.route = opts.route;

        this.eid = opts.eid || '';
        this.title = opts.title || '';
        this.content = opts.content || '';
        this.rqt_eid = opts.rqt_eid || '';
        this.onSubmit = opts.onSubmit || '';
        this.onCancel = opts.onCancel || '';

        this.initHtml();
        this.initSubmitCallback();
        this.initCancelCallback();
    }

    IdeaForm.prototype = {
        initHtml: function () {
            this.$ctn.html(
                tpl(require('tpl/idea-form.tpl'), {
                    eid: this.eid,
                    title: this.title,
                    content: this.content,
                    rqt_eid: this.rqt_eid,

                    title_placeholder: z.trans('idea-title'),
                    content_placeholder: z.trans('idea-content'),
                    submit: z.trans('submit'),
                    cancel: z.trans('cancel')
                })
            );
        },
        initSubmitCallback: function () {
            var self = this;
            require.ensure([], function () {
                self.zeditor = require('zjs/z.editor')(self.$ctn.find('.zeditor'), ['image', 'bold', 'insertUnorderedList', 'justifyLeft', 'justifyCenter']);

                self.zeditor.autoheight();

                self.$ctn.find('form').on('submit', function (e) {
                    z.cancelEvent(e);
                    if (!self.zeditor.text()) {
                        return false;
                    }
                    net.routePost(self.route, new window.FormData(this), function (pack) {
                        if (self.onSubmit) {
                            self.onSubmit.call(self, pack);
                        }
                    });
                    return false;
                });
            });

        },
        initCancelCallback: function () {
            var self = this;
            this.$ctn.find('.cancel').on('click', function () {
                if (self.onCancel) {
                    self.onCancel.call(self);
                }
            });
        },
        setRoute: function (route) {
            this.route = route;
            return this;
        },
        clear: function () {
            this.setEid('');
            this.setRoute('');
            this.setContent('');
            this.setRqtEid('');
        },
        setEid: function (eid) {
            this.eid = eid;
            this.setInputValue('eid', eid);
        },
        setContent: function (content) {
            form_util.setZeditorContent(this.zeditor, this.$ctn, content);
            return this;

        },
        setRqtEid: function (rqt_eid) {
            this.rqt_eid = rqt_eid;
            this.setInputValue('rqt_eid', rqt_eid);
        },
        setInputValue: function (name, value) {
            this.$ctn.find('input[name="' + name + '"]').prop('value', value);
        }
    };

    return IdeaForm;
});
