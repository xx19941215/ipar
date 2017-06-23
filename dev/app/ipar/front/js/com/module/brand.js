define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.pop-panel',
    'zjs/z.simple-template-engine'
], function (z, s, net, panel, tpl) {
    'use strict';

    var $brand_edit = s('.brand-edit'),
        $brand_product_list,
        product_eid,
        current_page = 1,
        brand_panel;

    function show_brand_list()
    {
        var fd = new window.FormData();
        fd.append('page', current_page);
        fd.append('product_eid', product_eid);

        ajax('brand_list', fd, function (data) {
            var pack = z.pack(data),
                i,
                list,
                stack = [];

            if (!pack.isOk()) {
                return;
            }

            list = pack.getItem('list');
            if (list.length <= 0) {
                return;
            }

            for (i in list) {
                if (!list.hasOwnProperty(i)) {
                    continue;
                }
                append_brand_product_row(list[i]);
            }
            current_page += 1;
        });
    }

    function append_brand_product_row(row)
    {
        var html_tpl = require('tpl/brand-product-row.tpl'),
            $item;

        if (s('#brand-product-' + row.brand_product_id).length == 1) {
            return;
        }

        $item = s.create('div');
        $item.addClass('item clearfix');
        $item.html(tpl(html_tpl, row));
        $brand_product_list.append($item);

        $item.find('.brand-op').on('click', function () {
            var $self = s(this),
                fd = new window.FormData(),
                route;

            if ($self.hasClass('vote')) {
                route = 'brand_vote';
            } else {
                route = 'brand_unvote';
            }
            fd.append('brand_product_id', row.brand_product_id);
            ajax(route, fd, function (data) {
                if (z.pack(data).isOk()) {
                    $self.removeClass('vote unvote');
                    if (route == 'brand_vote') {
                        $self.addClass('unvote');
                    } else {
                        $self.addClass('vote');
                    }
                }
            });
        });
    }

    function init_brand_product_form()
    {
        s('.brand-product-form').on('submit', function () {
            var fd = new window.FormData(this);
            fd.append('product_eid', product_eid);
            ajax('brand_save', fd, function (data) {
                var pack = z.pack(data);
                if (pack.isOk()) {
                    append_brand_product_row(pack.getItem('brand_product'));
                }
            });
        });

        s('.brand-product-form .input').on('keyup', function () {
            var $self = s(this),
                str = $self.prop('value'),
                $brand_product_item = s('.brand-product-list > .item');

            $brand_product_item.each(function (item) {
                var $item = s(item),
                    $brand_title = $item.find('.brand-title'),
                    text = $brand_title.text();

                if (text.match(new RegExp(str, 'i'))) {
                    $item.show();
                } else {
                    $item.hide();
                }
            });
        });
    }

    function init_brand_product_list()
    {
        if (!$brand_product_list) {
            $brand_product_list = s('.brand-product-list');
        }
    }

    function ajax(route, fd, callback) {
        var url = z.config('api')[route];
        if (!url) {
            console.log('route-not-found');
            return;
        }
        fd.append('_token', z.get_token());
        net.ajax({
            dataType: 'json',
            url: url,
            method: 'post',
            withCredentials: true
        })
        .done(callback)
        .send(fd);
    }

    function init() {
        brand_panel = panel({panel_class: 'brand-panel'});
        brand_panel.html(
            tpl(
                require('tpl/brand-box.tpl'),
                {submit: z.trans('submit')}
            )
        );

        $brand_edit.on('click', function () {
            var $self = s(this);
            product_eid = parseInt($self.attr('data-product_eid'));
            brand_panel.show();
            show_brand_list();
        });
        brand_panel.onHide(function () {
            var fd = new window.FormData(),
                html_tpl = require('tpl/brand-hot-item.tpl');

            fd.append('product_eid', product_eid);
            fd.append('cpp', 3);
            ajax('brand_list', fd, function (data) {
                var pack = z.pack(data),
                    arr = [];

                if (pack.isOk()) {
                    pack.getItem('list').forEach(function (item) {
                        arr.push(tpl(html_tpl, item));
                    });
                    if (arr.length > 0) {
                        s('.brand-hot-list').html(arr.join(''));
                    }
                }
            });
        });

        init_brand_product_form();
        init_brand_product_list();
    }

    if ($brand_edit.length > 0) {
        init();
    }

});
