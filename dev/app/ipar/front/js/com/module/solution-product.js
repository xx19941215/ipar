define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    "zjs/z.pop-panel",
    'zjs/z.simple-template-engine',
    'module/load-zimg'
], function (z, s, net, panel, tpl, load_zimg) {
    'use strict';

    var SolutionProduct;

    SolutionProduct = function (opts) {
        opts = opts || {};

        this.panel = panel({panel_class: 'form-wrap'});

        this.rqt_eid = opts.rqt_eid || '';

        this.panel.html(
            tpl(require('tpl/solution-product-form.tpl'), {
                eid: opts.eid || '',
                rqt_eid: this.rqt_eid,
                title: opts.title || '',
                content: opts.content || '',
                //url: opts.url || '',
                title_placeholder: opts.title_placeholder || z.trans('product-title'),
                content_placeholder: opts.content_placeholder || z.trans('product-content'),
                //url_placeholder: opts.url_placeholder || z.trans('product-url'),
                submit: z.trans('submit'),
                cancel: z.trans('cancel'),
                panel_title: z.trans('solution-product')
            })
        );

        this.is_waiting = false;
        this.init();
    };

    SolutionProduct.prototype = {
        init: function () {
            var self = this,
                $panel = this.panel.$panel;

            require.ensure([], function() {
                require('zjs/z.editor')($panel.find('.zeditor'), ['image', 'bold', 'insertUnorderedList', 'justifyLeft', 'justifyCenter']).autoheight();
                $panel.find('.solution-product-form').on('submit', function (e) {
                    z.cancelEvent(e);
                    net.routePost('rqt_save_product', new window.FormData(this), function (pack) {
                        if (pack.isOk()) {
                            //window.location = '/';
                            window.location = window.location;
                            return;
                        }
                        window.alert('save-failed');
                    });
                    return false;
                });
                $panel.find('input[name="title"]').on('keyup', function () {
                    self.suggest(s(this).prop('value'));
                    //console.log(s(this).prop('value'));
                });
            });

            self.getRecommendForm().find('.close').on('click', function () {
                self.getSubmitForm().removeClass('hide');
                self.getRecommendForm().addClass('hide');
            });

            self.panel.$panel.find('.cancel').on('click', function () {
                self.panel.hide();
            });

            self.getRecommendForm().on('submit', function (e) {
                z.cancelEvent(e);
                net.routePost('rqt_save_solution', new window.FormData(this), function (pack) {
                    if (pack.isOk()) {
                        //window.location = '/';
                        window.location = window.location;
                        return;
                    }
                    window.alert('save-failed');
                });
                return false;
            });
        },
        setRqtEid: function (rqt_eid) {
            this.rqt_eid = rqt_eid;
            this.panel.$panel.find('input[name="rqt_eid"]').prop('value', parseInt(rqt_eid));
            return this;
        },
        suggest: function (query) {
            var self = this;

            if (self.is_waiting) {
                return;
            }
            self.is_waiting = true;
            setTimeout(function () {
                query = self.getQuery();
                if (!query) {
                    self.is_waiting = false;
                    return;
                }

                net.routePost(
                    'suggest',
                    {query: query, require: 'product'},
                    function (pack) {
                        if (!pack.isOk()) {
                            console.log(pack);
                            return;
                        }

                        self.is_waiting = false;

                        //console.log(pack.getItem('products'));
                        self.loadSearchResult(pack.getItem('products'));

                        if (query != self.getQuery()) {
                            self.suggest(query);
                            return;
                        }
                    }
                );

            }, 500);
        },
        loadSearchResult: function (products) {
            var $search_result = this.panel.$panel.find('.search-result'),
                i, product, list = [],
                len = products.length,
                self = this;

            $search_result.html('');
            if (products.length <= 0) {
                $search_result.addClass('hide');
                return;
            }

            for (i = 0; i < len; i++) {
                product = products[i];
                //list.push('<a href="' + product.url + '">' + product.title + '</a>');
                list.push('<a href="javascript:;" data-eid="' + product.eid+ '">' + product.title + '</a>');
            }
            if (list.length > 0) {
                $search_result.html('<ul><li>' + list.join('</li><li>') + '</li></ul>');
                $search_result.removeClass('hide');
                $search_result.find('a').on('click', function () {
                    self.loadRecommendProduct(s(this).attr('data-eid'));
                });
            }
        },
        loadRecommendProduct: function (eid) {
            var self = this;
            self.getSubmitForm().addClass('hide');
            self.getRecommendForm().removeClass('hide');

            self.getRecommendForm().find('.title').html('');
            self.getRecommendForm().find('.content').html('');

            net.routePost('fetch', {eid: eid}, function (pack) {
                var entity;
                if (!pack.isOk()) {
                    console.log('fetch entity failed');
                    return;
                }
                entity = pack.getItem('entity');
                self.getRecommendForm().find('.title').html(entity.title);
                self.getRecommendForm().find('.content').html(entity.content);
                self.getRecommendForm().find('input[name="dst_eid"]').prop('value', entity.eid);
                load_zimg(self.getRecommendForm());
            });
        },
        getInputTitle: function () {
            if (this.$input_title) {
                return this.$input_title;
            }
            this.$input_title = this.panel.$panel.find('input[name="title"]');
            return this.$input_title;
        },
        getQuery: function () {
            return this.getInputTitle().prop('value');
        },
        getSubmitForm: function () {
            if (this.$submit_form) {
                return this.$submit_form;
            }
            this.$submit_form = this.panel.$panel.find('.solution-product-form');
            return this.$submit_form;
        },
        getRecommendForm: function () {
            if (this.$recommend_form) {
                return this.$recommend_form;
            }
            this.$recommend_form = this.panel.$panel.find('.solution-recommend-form');
            this.$recommend_form.on('submit', function () {
                console.log('recommend form');
            });
            return this.$recommend_form;
        },
        show: function () {
            this.panel.show();
        }
    };

    return function(opts) {
        return new SolutionProduct(opts);
    };
});
