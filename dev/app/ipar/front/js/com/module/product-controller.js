define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    "zjs/z.pop-panel",
    'module/product-form',
    'module/recommend-form',
    'module/message'
], function(z, s, net, pop_panel, ProductForm, RecommendForm, Message) {
    'use strict';

    function ProductController() {
        var self = this;

        this.panel = pop_panel({panel_class: 'form-wrap'});
        this.$submit_ctn = s.create('div').addClass('submit-ctn');
        this.$recommend_ctn = s.create('div').addClass('recommend-ctn hide');

        this.panel.append(this.$submit_ctn);
        this.panel.append(this.$recommend_ctn);

        this.product_form  = new ProductForm({
            $ctn: this.$submit_ctn,
            onRecommend: function (product) {
                self.loadRecommendForm(product);
            },
            onSubmit: function (product) {
                self.panel.hide();
                if (self.onSubmit) {
                    self.onSubmit.call(this, product);
                    return;
                }

                self.getMessage().addTopMessage(product.getItem('html_message'));
                self.product_form.clear();
            },
            onCancel: function () {
                self.panel.hide();
            }
        });
    }

    ProductController.prototype = {
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
                this.product_form.setRoute(opts.submit_route);
            }
            if (opts.recommend_route) {
                this.recommend_route = opts.recommend_route;
                //this.getRecommendForm().setRoute(opts.recommend_route);
            }

            if (opts.eid) this.product_form.setEid(opts.eid);
            if (opts.title) this.product_form.setTitle(opts.title);
            if (opts.content) this.product_form.setContent(opts.content);
            if (opts.rqt_eid) {
                this.product_form.setRqtEid(opts.rqt_eid);
                this.src_type_key = 'rqt';
                this.src_eid = opts.rqt_eid;
                /*
                this.getRecommendForm()
                    .setSrcType('rqt')
                    .setSrcEid(opts.rqt_eid);
                */
            }

            this.panel.show();
        },
        loadRecommendForm: function (product) {
            this.$submit_ctn.addClass('hide');
            this.$recommend_ctn.removeClass('hide');

            this.getRecommendForm().load({
                route: this.recommend_route || '',
                src_type_key: this.src_type_key || '',
                src_eid: this.src_eid || '',
                dst_type_key: 'product',
                dst_eid: product.eid,
                title: product.title,
                content: product.content,
                url: product.url
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

    return ProductController;
});
