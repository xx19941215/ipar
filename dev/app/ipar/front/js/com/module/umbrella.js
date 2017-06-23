define([
    "zjs/z",
    "zjs/z.selector",
    "module/like"
], function (z, s, like) {
    "use strict";
    function scrollListen() {
        $('.choose').each(function (index) {
            var scrollH = $(window).scrollTop(),
                limit = $(window).height() / 2,
                top = $(this).offset().top,
                eleHeight = $(this).height();
            if (scrollH + limit < top || top + eleHeight - scrollH < limit) {
                $(".anchor").find("li").eq(index).removeClass("current");
            } else {
                $(".anchor").find("li").eq(index).addClass("current");
            }
        });
    }

    function page_umbrella() {
        $(window).on('scroll load', function () {
            if ($(window).scrollTop() + 180 > $('.culture').offset().top) {
                $('.anchor').show();
            } else {
                $('.anchor').hide();
            }
            scrollListen();
        });
    }


    if (s('.ipar-ui-page-activity_foundation').length > 0) {
        require.ensure([], function () {
            require('scriptjs')('//cdn.bootcss.com/jquery/2.2.4/jquery.min.js', function () {
                require('scriptjs')('http://static.runoob.com/assets/jquery-validation-1.14.0/dist/jquery.validate.min.js', page_umbrella);
            })
        });
    }
    like.init();
});
