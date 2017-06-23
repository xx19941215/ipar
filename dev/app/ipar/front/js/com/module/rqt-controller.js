define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    "zjs/z.pop-panel",
    'module/rqt-form',
    'module/recommend-form',
    'module/message'
], function(z, s, net, pop_panel, RqtForm, RecommendForm, Message) {
    'use strict';

    function RqtController() {
        var self = this;

        this.panel = pop_panel({panel_class: 'form-wrap'});
        this.$submit_ctn = s.create('div').addClass('submit-ctn');
        this.$recommend_ctn = s.create('div').addClass('recommend-ctn hide');

        this.panel.append(this.$submit_ctn);
        this.panel.append(this.$recommend_ctn);

        this.rqt_form  = new RqtForm({
            $ctn: this.$submit_ctn,
            onRecommend: function (rqt) {
                self.loadRecommendForm(rqt);
            },
            onSubmit: function (rqt) {
                self.panel.hide();
                if (self.onSubmit) {
                    self.onSubmit.call(self, rqt);
                    return;
                }

                self.getMessage().addTopMessage(rqt.getItem('html_message'));
                self.rqt_form.clear();
            },
            onCancel: function () {
                self.panel.hide();
            }
        });
    }

    RqtController.prototype = {
        getMessage: function () {
            if (this.message) {
                return this.message;
            }
            this.message = new Message();
            return this.message;
        },
        showPanel: function (opts) {
            if (opts.panel_title) {
                this.panel.setPanelTitle(opts.panel_title);
            }
            if (opts.submit_route) {
                this.rqt_form.setRoute(opts.submit_route);
            }
            if (opts.recommend_route) {
                this.recommend_route = opts.recommend_route;
            }
            if (opts.eid) this.rqt_form.setEid(opts.eid);
            if (opts.title) this.rqt_form.setTitle(opts.title);
            if (opts.content) this.rqt_form.setContent(opts.content);

            if (opts.product_eid) {
                this.rqt_form.setProductEid(opts.product_eid);
                this.src_type_key = 'product';
                this.src_eid = opts.product_eid;
            }
            if (opts.type_key) {
                this.rqt_form.setTypeKey(opts.type_key);
                this.dst_type_key = opts.type_key;
            }

            this.panel.show();
        },
        loadRecommendForm: function (rqt) {
            this.$submit_ctn.addClass('hide');
            this.$recommend_ctn.removeClass('hide');

            this.getRecommendForm().load({
                route: this.recommend_route || '',
                src_type_key: this.src_type_key || '',
                src_eid: this.src_eid || '',
                dst_type_key: this.dst_type_key || 'rqt',
                dst_eid: rqt.eid,
                title: rqt.title,
                content: rqt.content,
                url: rqt.url
            });
        },
        getRecommendForm: function () {
            var self = this;

            if (this.recommend_form) {
                return this.recommend_form;
            }
            this.recommend_form = new RecommendForm({
                $ctn: this.$recommend_ctn,
                onClose: function () {
                    self.$submit_ctn.removeClass('hide');
                    self.$recommend_ctn.addClass('hide');
                },
                onCancel: function () {
                    self.$submit_ctn.removeClass('hide');
                    self.$recommend_ctn.addClass('hide');
                },
                onSubmit: function (pack) {
                    self.panel.hide();
                    if (self.onSubmit) {
                        self.onSubmit.call(this, pack);
                        return;
                    }

                    self.getMessage().addTopMessage(pack.getItem('html_message'));
                    self.recommend_form.clear();
                    self.$submit_ctn.removeClass('hide');
                    self.$recommend_ctn.addClass('hide');
                }
            });
            return this.recommend_form;
        }
    };

    return RqtController;
});
