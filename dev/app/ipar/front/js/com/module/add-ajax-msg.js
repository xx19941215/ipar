define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.simple-template-engine',
    'base/ipar'
], function (z, s, net, tpl, ipar) {
    'use strict';

    //sort:1  按时间排序　sort:0 按热度排序
    function initSortToggle() {

        s('.sort-toggle .sort-hot').on('click', function () {
            //clean div
            s('.story-set-wrap').html('');
            s('.product-set-wrap').html('');
            s('.box-set-wrap').html('');

            s('.loading')
                .attr('data-page', 1)
                .attr('data-sort', 0);
            s('.sort-toggle').addClass('sort-hot');
            s('.sort-toggle .sort-hot').addClass('sort-visited');
            s('.sort-toggle').removeClass('sort-date');
            s('.sort-toggle .sort-date').removeClass('sort-visited');
            ipar.init();

        });

        s('.sort-toggle .sort-date').on('click', function () {
            //clean div
            s('.story-set-wrap').html('');
            s('.product-set-wrap').html('');
            s('.box-set-wrap').html('');

            s('.loading')
                .attr('data-page', 1)
                .attr('data-sort', 1);
            s('.sort-toggle').addClass('sort-date');
            s('.sort-toggle .sort-date').addClass('sort-visited');
            s('.sort-toggle').removeClass('sort-hot');
            s('.sort-toggle .sort-hot').removeClass('sort-visited');
            ipar.init();
        });
    }

    if (document.getElementsByClassName('sort-toggle')[0]) {
        initSortToggle();
    }

    if (s('.story-set-wrap').length || s('.box-set-wrap').length) {
        s('.sort-toggle').removeClass('hide');
    }
});