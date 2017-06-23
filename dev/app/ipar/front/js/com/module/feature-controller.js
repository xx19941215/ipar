define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    "zjs/z.pop-panel",
    'module/feature-form',
    'module/recommend-form',
    'module/message'
], function(z, s, net, pop_panel, FeatureForm, RecommendForm, Message) {
    'use strict';

    function FeatureController() {
        var self = this;

        this.panel = pop_panel({panel_class: 'form-wrap'});
        this.$submit_ctn = s.create('div').addClass('submit-ctn');
        this.$recommend_ctn = s.create('div').addClass('recommend-ctn hide');

        this.panel.append(this.$submit_ctn);
        this.panel.append(this.$recommend_ctn);

        this.feature_form  = new FeatureForm({
            $ctn: this.$submit_ctn,
            onRecommend: function (feature) {
                self.loadRecommendForm(feature);
            },
            onSubmit: function (feature) {
                self.panel.hide();
                if (self.onSubmit) {
                    self.onSubmit.call(this, feature);
                    return;
                }

                self.getMessage().addTopMessage(feature.getItem('html_message'));
                self.feature_form.clear();
            },
            onCancel: function () {
                self.panel.hide();
            }
        });
    }

    FeatureController.prototype = {
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
                this.feature_form.setRoute(opts.submit_route);
            }
            if (opts.recommend_route) {
                this.recommend_route = opts.recommend_route;
                //this.getRecommendForm().setRoute(opts.recommend_route);
            }

            if (opts.eid) this.feature_form.setEid(opts.eid);
            if (opts.title) this.feature_form.setTitle(opts.title);
            if (opts.content) this.feature_form.setContent(opts.content);
            if (opts.product_eid) {
                this.feature_form.setProductEid(opts.product_eid);
                this.src_type_key = 'product';
                this.src_eid = opts.product_eid;
            }

            this.panel.show();
        },
        loadRecommendForm: function (feature) {
            this.$submit_ctn.addClass('hide');
            this.$recommend_ctn.removeClass('hide');

            this.getRecommendForm().load({
                src_type_key: this.src_type_key || '',
                src_eid: this.src_eid || '',
                route: this.recommend_route || '',
                dst_type_key: 'feature',
                dst_eid: feature.eid,
                title: feature.title,
                content: feature.content,
                url: feature.url
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

    return FeatureController;
});
