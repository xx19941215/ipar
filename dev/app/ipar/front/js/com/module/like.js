define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.pop-panel',
    'zjs/z.simple-template-engine',
    'module/auth'
], function (z, s, net, panel, tpl, auth) {
    'use strict';

    var like = {
        init: function ($set) {
            $set = $set || 'body';
            $set = s($set);
            init($set);
        }
    }


    var $like = s('.entity .like'),
        $loading = s('.loading'),
        $like_count = s('.like-count'),
        $icon = s('.icon-like'),
        user_panel = panel();

    var $user_container = s('.user-container'),
        $user_body = s('.user-body'),
        $user_container_title = s('.user-container-title');


    $like.on('click', function () {
        var entity_eid = $loading.attr('data-eid');
        if (!auth.requireLogin()) {
            return;
        }
        likeEntity(entity_eid, $icon, $like_count);
    });

    $like_count.on('click', function () {
        var entity_eid = $loading.attr('data-eid');
        if (parseInt($like_count.html()) == 0) {
            return;
        }
        likeUser(entity_eid);
    });

    function likeEntity(eid, $icon, $like_count) {
        var route = 'like_entity',
            liked = $icon.hasClass('liked');

        if (liked) {
            route = 'unlike_entity';
        }

        net.routePost(route, {'eid': eid}, function (pack) {
            if (pack.isOk()) {
                if (liked) {
                    $icon.removeClass('liked');
                    $like_count.html(parseInt($like_count.html()) - 1);
                } else {
                    $icon.addClass('liked');
                    $like_count.html(parseInt($like_count.html()) + 1);
                }
            }
        });
    }

    function likeUser(eid) {
        net.routePost('like_user', {'eid': eid}, function (pack) {
            if (pack.isOk()) {
                $user_body.html("");
                $user_container_title.html(z.trans("like-user"));
                pack.items.eid = eid;
                showPanel(pack);
                $user_container.attr('data-url', 'like_user');
                user_panel.show();
            }
        });
    }

    var init = function ($set) {
        $set.find('.entity .like-list').on('click', function (e) {
            if (!auth.requireLogin()) {
                return;
            }
            var eid = this.parentElement.parentElement.getAttribute('data-id'),
                eid_class = ".eid-" + eid,
                $icon = s(eid_class + " .icon-like"),
                $like_count = s(eid_class + " .like-count");

            likeEntity(eid, $icon, $like_count);
        });
        $set.find('.like-count').on('click', function (e) {
            var eid = this.parentElement.parentElement.getAttribute('data-id');

            if (!auth.requireLogin()) {
                return;
            }

            if (this.innerHTML == 0) {
                return;
            }
            likeUser(eid);
        });
    }

    return like;
});