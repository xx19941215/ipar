define([
    'zjs/z',
    'zjs/z.selector'
], function (z, s) {
    'use strict';

    z.ready(function () {
        if (s('.zeditor').length > 0) {
            require.ensure([], function () {
                //require('./../scss/element/_zeditor.scss');
                var features = ['image', 'bold', 'insertUnorderedList', 'justifyLeft', 'justifyCenter', 'formatBlock', 'pasteHTML'];
                require('zjs/z.editor')('.zeditor', features).autoheight();
            });
        }

        if (s('.reportor').length > 0) {
            require.ensure([], function () {
                require('module/reportor');
            });
        }

        s('.zimg').each(function (item) {
            var $item = s(item),
                protocol = $item.attr('data-protocol') || 'http://',
                site = $item.attr('data-site') || 'static',
                host = z.config('site')[site].host,
                dir = $item.attr('data-dir'),
                name = $item.attr('data-name'),
                ext = $item.attr('data-ext');

            $item.attr('src', protocol + host + dir + '/' + name + '.' + ext);
        });
    });
});


