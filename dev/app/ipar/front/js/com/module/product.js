define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    "zjs/z.pop-panel",
    'zjs/z.simple-template-engine'
], function (z, s, net, panel, tpl) {
    'use strict';

    var Product;

    Product = function (opts) {
        opts = opts || {};

        this.panel = panel();

        this.panel.html(
            tpl(require('tpl/product-form.tpl'), {
                title: opts.title || '',
                content: opts.content || '',
                url: opts.url || '',
                title_placeholder: opts.title_placeholder || z.trans('product-title'),
                content_placeholder: opts.content_placeholder || z.trans('product-content'),
                url_placeholder: opts.url_placeholder || z.trans('product-url'),
                submit: z.trans('submit')
            })
        );

        this.init();
    };

    Product.prototype = {
        init: function () {
            var $panel = this.panel.$panel;
            require.ensure([], function() {
                require('zjs/z.editor')($panel.find('.zeditor')).autoheight();
                $panel.find('.product-form').on('submit', function (e) {
                    z.cancelEvent(e);
                    net.routePost('product_save', new window.FormData(this), function (pack) {
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
        return new Product(opts);
    };
});
