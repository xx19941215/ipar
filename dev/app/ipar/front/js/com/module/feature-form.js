define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.simple-template-engine',
    'module/suggest-recommend',
    'module/form-util'
], function (z, s, net, tpl, SuggestRecommend, form_util) {
    'use strict';

    function FeatureForm(opts) {
        opts = opts || {};
        if (!opts.$ctn) {
            console.log('ctn cannot be empty');
            return;
        }
        /*
        if (!opts.route) {
            console.log('route cannot be empty');
            return;
        }
        */

        this.$ctn = opts.$ctn;
        this.route = opts.route;

        this.eid = opts.eid || '';
        this.title = opts.title || '';
        this.content = opts.content || '';
        this.product_eid = opts.product_eid || '';

        this.onSubmit = opts.onSubmit || '';
        this.onRecommend = opts.onRecommend || '';
        this.onCancel = opts.onCancel || '';

        this.initHtml();
        this.initSubmitCallback();
        this.initCancelCallback();
        this.initRecommendCallback();
    }

    FeatureForm.prototype = {
        initHtml: function () {
            this.$ctn.html(
                tpl(require('tpl/feature-form.tpl'), {
                    eid: this.eid,
                    title: this.title,
                    content: this.content,
                    product_eid: this.product_eid,

                    title_placeholder: z.trans('feature-title'),
                    content_placeholder: z.trans('content-title'),
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
        initRecommendCallback: function () {
            var self = this,
                suggest_recommend;
            if (self.eid) {
                return;
            }

            suggest_recommend = new SuggestRecommend({
                $input: self.getInputTitleElm(),
                $search_result: self.getSearchResultElm(),
                filter: 'feature',
                onRecommend: self.onRecommend
            });
            /*
            self.$ctn.find('input[name="title"]').on('keyup', function () {
                self.suggest(s(this).prop('value'));
            });
            */
        },
        getInputTitleElm: function () {
            if (this.$input_title) {
                return this.$input_title;
            }
            this.$input_title = this.$ctn.find('input[name="title"]');
            return this.$input_title;
        },
        getSearchResultElm: function () {
            if (this.$search_result) {
                return this.$search_result;
            }
            this.$search_result = this.$ctn.find('.search-result');
            return this.$search_result;
        },
        setRoute: function (route) {
            this.route = route;
            return this;
        },
        clear: function () {
            this.setEid('');
            this.setRoute('');
            this.setTitle('');
            this.setContent('');
            this.setProductEid('');
            this.$search_result.addClass('hide');
        },
        setEid: function (eid) {
            this.eid = eid;
            this.setInputValue('eid', eid);
        },
        setTitle: function (title) {
            this.title = title;
            this.setInputValue('title', title);
            return this;
        },
        setContent: function (content) {
            form_util.setZeditorContent(this.zeditor, this.$ctn, content);
            return this;
        },
        setProductEid: function (product_eid) {
            this.product_eid = product_eid;
            this.setInputValue('product_eid', product_eid);
        },
        setTypeKey: function (type_key) {
            this.type_key = type_key;
            this.setInputValue('type_key', type_key);
        },
        setInputValue: function (name, value) {
            this.$ctn.find('input[name="' + name + '"]').prop('value', value);
        }
    };

    return FeatureForm;
});
