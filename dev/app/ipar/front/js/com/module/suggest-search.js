define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'module/suggest',
    'module/rqt-controller',
    'module/product-controller',
    'module/auth'
], function(z, s, net, Suggest, RqtController, ProductController, auth) {
    'use strict';

    var $input = s('.input-search input[name="query"]'),
        $suggest = s('.input-search .suggest'),
        $search_result_wrap = s('.suggest .search-result-wrap'),
        $search_all_wrap = s('.suggest .search-all-wrap'),
        //$button_wrap = s('.suggest .button-wrap'),
        rqt_controller,
        product_controller,
        is_waiting = false;


    function clear_search_result()
    {
        $search_result_wrap.html('');
        $search_all_wrap.addClass('hide');
        return;
    }

    function load_search_result(data)
    {
        var type,
            title,
            entities,
            html = '';

        $search_result_wrap.html('');
        for (type in data) {
            if (!data.hasOwnProperty(type)) {
                continue;
            }
            title = data[type].title;
            entities = data[type].entities;

            if (!entities || entities.length <= 0) {
                continue;
            }

            html += [
                '<div class="wrap ', type, '">',
                '<h3 class="title">', title, '</h3>',
                '<div class="list">',
                to_html_list(entities),
                '</div>',
                '</div>',
            ].join('');
        }

        if (html) {
            $search_result_wrap.html(html);
            return;
        }

        //$search_result_wrap.html(z.trans('no-matches'));
        $search_result_wrap.html('');

    }

    function to_html_list(arr)
    {
        var len = arr.length,
            i = 0,
            list = [],
            item;

        if (!arr || arr.length <= 0) {
            return '';
        }

        for (i = 0; i < len; i++) {
            item = arr[i];
            list.push('<a href="' + item.url + '">' + item.title + '</a>');
        }

        if (list.length > 0) {
            return '<ul><li>' + list.join('</li><li>') + '</li></ul>';
        }

        return ''
    }

    function get_rqt_controller()
    {
        if (rqt_controller) {
            return rqt_controller;
        }

        rqt_controller = new RqtController();
        return rqt_controller;
    }

    function get_product_controller()
    {
        if (product_controller) {
            return product_controller;
        }

        product_controller = new ProductController();
        return product_controller;
    }

    (new Suggest).init({
        $input: $input,
        onFocus: function () {
            $suggest.removeClass('hide');
        },
        onWaiting: function () {
            $search_all_wrap.addClass('hide');
        },
        onNoQuery: function () {
            clear_search_result();
        },
        onLoad: function (pack) {
            $search_all_wrap.removeClass('hide');
            load_search_result({
                rqt: {title: z.trans('rqt'), entities: pack.getItem('rqts')},
                product: {title: z.trans('product'), entities: pack.getItem('products')},
                article: {title: z.trans('article'), entities: pack.getItem('articles')},
                user: {title: z.trans('user'), entities: pack.getItem('users')},
                company: {title: z.trans('company'), entities: pack.getItem('companys')},
                correct: {title: z.trans('if-you-want-search'), entities: pack.getItem('correct')},
                hotsuggest: {title: z.trans('hot-search-list'), entities: pack.getItem('hotsuggest')}
            });
        }
    });


    z.addEvent(window.document, 'click', function(e) {
        if (!s('.input-search').contains(e.target)) {
            s('.input-search .suggest').addClass('hide');
        }
    });

    s('.add-rqt').on('click', function () {
        if(!auth.requireLogin()) {
            return;
        }
        get_rqt_controller().showPanel({
            panel_title: z.trans('create-rqt'),
            submit_route: 'rqt_save'
        });
    });

    s('.suggest .add-product').on('click', function () {
        get_product_controller().showPanel({
            panel_title: z.trans('create-product'),
            submit_route: 'product_save'
        });
    });

    return {
        get_rqt_controller: get_rqt_controller
    };
});
