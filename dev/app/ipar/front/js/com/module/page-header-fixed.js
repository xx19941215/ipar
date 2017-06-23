define([
    'zjs/z',
    'zjs/z.selector'
], function (z, s) {
    'use strict';

    var $menu_box = s('.menu-box'),
        $toggle = s('.user-logined'),
        $sign_wrap = s('.sign-wrap'),
        $input = s('.input-search input');

    function toggleMenuBox() {
        if ($menu_box.isHidden()) {
            showMenuBox();
        } else {
            hideMenuBox();
        }
    }

    function showMenuBox() {
        $menu_box.removeClass('hide');
        $menu_box.addClass('show');
    }

    function hideMenuBox() {
        $menu_box.removeClass('show');
        $menu_box.addClass('hide');
    }

    $sign_wrap.on('mouseover', function (e) {
        toggleMenuBox();
    });

    $sign_wrap.on('mouseout', function (e) {
        toggleMenuBox();
    });

    $sign_wrap.find('.user-avt').on('click',function (e) {
        e.preventDefault();
    });

    $sign_wrap.find('.user-avt').on('touchend',function (e) {
        this.setAttribute('href', '#');
    });

    $input.on('focus', function (e) {
        if (window.innerWidth < 768) {
            s('.input-search').parent().style('width', 'calc( 100% - 100px)');
            s('.cancel-search').removeClass('hide');
        }
    }).on('blur', function (e) {
        if (window.innerWidth < 768) {
            s('.input-search').parent().style('width', 'calc( 100% - 165px)');
            s('.cancel-search').addClass('hide');
        }
    });

    s('.page-body').on('click', function (e) {
        if (!$menu_box.isHidden() && !$menu_box.contains(e.target)) {
            hideMenuBox();
        }
    });

    s('.cancel-search').on('touchend', function (e) {
        e.stopPropagation();
        s('.cancel-search').addClass('hide');
        s('.search-all-wrap').addClass('hide');
        s('.search-result-wrap').addClass('hide');
        s('.suggest').addClass('hide');
    })

});
