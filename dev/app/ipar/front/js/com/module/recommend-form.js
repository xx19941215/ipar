define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.simple-template-engine',
    'zjs/z.net',
    'module/load-zimg'
], function (z, s, tpl, net, load_zimg) {
    'use strict';

    function RecommendForm(opts) {
        opts = opts || {};

        this.$ctn = opts.$ctn;
        this.route = opts.route;

        this.src_type_key = opts.src_type_key || '';
        this.src_eid = opts.src_eid || '';
        this.dst_type_key = opts.dst_type_key || '';
        this.dst_eid = opts.dst_eid || '';
        this.title = opts.title || '';
        this.content = opts.content || '';
        this.url = opts.url || '';
        this.onClose = opts.onClose || '';
        this.onCancel = opts.onCancel || '';
        this.onSubmit = opts.onSubmit || '';

        this.initHtml();
        this.initSubmitCallback();
        this.initCloseCallback();
        this.initCancelCallback();
    }

    RecommendForm.prototype = {
        initHtml: function () {
            this.$ctn.html(
                tpl(require('tpl/recommend-form.tpl'), {
                    src_type_key: this.src_type_key,
                    src_eid: this.src_eid,
                    dst_type_key: this.dst_type_key,
                    dst_eid: this.dst_eid,
                    title: this.title,
                    content: this.content,

                    cancel: z.trans('cancel'),
                    submit: z.trans('submit')
                })
            );
        },
        initSubmitCallback: function () {
            var self = this;

            this.$ctn.find('form').on('submit', function (e) {
                z.cancelEvent(e);
                if (!self.route) {
                    return;
                }
                net.routePost(self.route, new window.FormData(this), function (pack) {
                    self.onSubmit.call(self, pack);
                });
                return false;
            });
        },
        initCloseCallback: function () {
            var self = this;
            this.$ctn.find('.close').on('click', function () {
                self.clear();
                if (self.onClose) self.onClose.call(self, this);
            });
        },
        initCancelCallback: function () {
            var self = this;
            this.$ctn.find('.cancel').on('click', function () {
                self.clear();
                if (self.onCancel) {
                    self.onCancel.call(self, this);
                }
            });
        },
        clear: function () {
            var self = this;
            self.setTitle('');
            self.setContent('');
            self.setRoute('');
            self.setInputValue('src_type_key', '');
            self.setInputValue('src_eid', '');
            self.setInputValue('dst_type_key', '');
            self.setInputValue('dst_eid', '');
        },
        load: function (data) {
            var title;

            if (data.src_type_key)
                this.setInputValue('src_type_key', data.src_type_key);
            if (data.src_eid)
                this.setInputValue('src_eid', data.src_eid);
            if (data.dst_type_key)
                this.setInputValue('dst_type_key', data.dst_type_key);
            if (data.dst_eid)
                this.setInputValue('dst_eid', data.dst_eid);

            if (data.route)
                this.setRoute(data.route);

            title = data.title || '';
            if (this.route) {
                this.getButtonWrapElem().removeClass('hide');
            } else {
                title = '<a href="' + (data.url || 'javascript:;') + '">' + title + '</a>';
                this.getButtonWrapElem().addClass('hide');
            }
            this.setTitle(title);

            if (data.content)
                this.setContent(data.content);

        },
        setSrcTypeKey: function (src_type_key) {
            this.setInputValue('src_type_key', src_type_key);
            return this;
        },
        setSrcEid: function (src_eid) {
            this.setInputValue('src_eid', src_eid);
            return this;
        },
        setRoute: function (route) {
            this.route = route;
            return this;
        },
        setTitle: function (title) {
            this.title = title;
            this.$ctn.find('.title').html(title);
            return this;
        },
        setContent: function (content) {
            var $content = this.$ctn.find('.content');
            this.content = content;
            $content.html(content);
            load_zimg($content);
            return this;
        },
        setInputValue: function (name, value) {
            this[name] = value;
            this.$ctn.find('input[name="' + name + '"]').prop('value', value);
            return this;
        },
        getButtonWrapElem: function () {
            if (this.$button_wrap) {
                return this.$button_wrap;
            }
            this.$button_wrap = this.$ctn.find('.button-wrap');
            return this.$button_wrap;
        }
    };

    return RecommendForm;
});
