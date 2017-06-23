define([
    'zjs/z',
    'zjs/z.selector'
], function(z, s) {
    'use strict';

    return function (selector) {
        var items;
        selector = selector || 'body';
        items = s(selector).find('.zimg');
        items.each(function(item) {
            var $item = s(item),
                protocol = $item.attr('data-protocol') || 'http://',
                site = $item.attr('data-site') || 'static',
                host = z.config('site')[site].host,
                dir = $item.attr('data-dir'),
                name = $item.attr('data-name'),
                ext = $item.attr('data-ext');

            $item.attr('src', protocol + host + dir + '/' + name + '.' + ext);
        });
    };
});
