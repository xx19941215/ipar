define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.pop-panel',
    'zjs/z.simple-template-engine',
    'module/auth'
], function (z, s, net, panel, tpl, auth) {
    'use strict';

    var eid,
        dst_uid,
        current_uid,
        arr = [],
        trans_arr,
        page = 2,
        loading_able = true,
        user_panel = panel(),
        $user_follow,
        $followed_user,
        $following_user,
        $entity_follow,
        $info = s('#follow-status'),
        $user_container = s('.user-container'),
        $user_container_title = s('.user-container-title'),
        $user_body = s('.user-body'),
        $user_info = s('.user-info'),
        $followed_count = s('.followed-count'),
        $following_count = s('.following-count'),
        follow_tpl = require('tpl/follow-set.tpl');


    $entity_follow = s('.entities');
    if ($entity_follow.length > 0) {
        eid = $info.attr('data-eid');
    }
    $user_follow = s('.user');
    if ($user_follow.length > 0) {
        dst_uid = $info.attr('data-uid');
    }

    $entity_follow.on('click', function () {
        if (!auth.requireLogin()) {
            return;
        }
        var $info = s('#follow-status');
        var followStatus = $info.attr('class');
        var fd = new window.FormData();
        var url = "";
        fd.append("eid", eid);

        if (followStatus == 'unfollow') {
            url = z.config('api').follow_entity;
        } else {
            url = z.config('api').unfollow_entity;
        }
        ajax(show, url, fd);
    });

    $user_follow.on("click", function () {

        if (!auth.requireLogin()) {
            return;
        }
        var $info = s('#follow-status');
        var followStatus = $info.attr('class');
        var fd = new window.FormData();
        var url = "";

        fd.append("uid", dst_uid);

        if (followStatus == "unfollow") {
            url = z.config('api').follow_user;
        } else {
            url = z.config('api').unfollow_user;
        }
        ajax(show, url, fd);
    });

    var $group_follow = s('.group');
    $group_follow.on('click', function () {

        if (current_uid == 0) {
            return;
        }
        var $info = s('#follow-status');
        var followStatus = $info.attr('class');
        var fd = new window.FormData();
        var url = "",
            gid = s('.gid')[0].value;

        fd.append("gid", gid);

        if (followStatus == "unfollow") {
            url = z.config('api').follow_group;
        } else {
            url = z.config('api').unfollow_group;
        }
        ajax(show, url, fd);
    });
    function ajax(show, url, fd, obj) {

        fd.append("_token", config.token);
        net.ajax({
            dataType: 'json',
            url: url,
            method: 'post',
            withCredentials: true
        }).done(function (data) {
            if (obj) {
                show(z.pack(data), obj);
            } else {
                show(z.pack(data));
            }
            if ($user_follow.length > 0) {
                getCommonUserSet();
            }
        }).send(fd);
    }

    function show(data) {
        if (data.isOk()) {

            if (data.getItem('is_following')) {
                $info.attr('class', 'followed');
            } else {
                $info.attr('class', 'unfollow');
            }

            $followed_count.html(data.getItem('followed_count'));
            $following_count.html(data.getItem('following_count'));
            if (data.getItem('followed_users')) {

                $user_info.html(data.getItem('followed_users').join(''));
            }
        }
    }


    $followed_user = s('.followed-count');
    $followed_user.on('click', function () {
        if (parseInt(this.innerText, 10) == 0) {
            return;
        }
        var fd = new window.FormData();
        var url = "";
        if ($user_follow.length > 0) {
            url = z.config('api').followed_users;
            fd.append("uid", dst_uid);
            $user_container.attr('data-url', 'followed_users');
            $user_container_title.html(z.trans("followed-user"));
        }
        if ($entity_follow.length > 0) {
            url = z.config('api').followed_entitys;
            fd.append("eid", eid);
            $user_container.attr('data-url', 'followed_entitys');
            $user_container_title.html(z.trans("followed-user"));

        }

        $user_body.html("");
        ajax(showPanel, url, fd);
    });


    $following_user = s('.following-count');
    $following_user.on('click', function () {
        if (parseInt(this.innerText, 10) == 0) {
            return;
        }
        var url = z.config('api').following_users;
        var fd = new window.FormData();

        fd.append("uid", dst_uid);
        $user_container.attr('data-url', 'following_users');
        $user_body.html("");
        ajax(showPanel, url, fd);
        $user_container_title.html(z.trans("following-user"));
    });

    window.showPanel = function (data, append) {

        if (!append) {
            arr = [];
        }

        $user_body.html("");
        loading_able = true;

        if (data.isOk()) {

            var followed_users = data.getItem('followed_users');
            for (var index in followed_users) {

                if (current_uid == 0) {
                    followed_users[index].is_following = "unfollow";
                }
                arr.push(tpl(follow_tpl, z.extend(followed_users[index], trans_arr)));
            }
            if (followed_users.length < 5) {
                loading_able = false;
            }

            eid = data.getItem('eid') || eid;

            page = parseInt(data.getItem('page'), 10) + 1;
            $user_body.html(arr.join(''));
            user_panel.append($user_container);
            $user_container.removeClass("hide");
            user_panel.show();
            init_user_container($user_container);

        }
    }

    window.init_user_container = function ($user_container) {
        $user_container.find('.panel-follow-status').on('click', function () {

            if (current_uid == 0) {
                return;
            }
            var url = "";
            var fd = new window.FormData();
            var panel_followStatus = this.getAttribute('data-status');
            var uid = this.getAttribute('data-uid');

            fd.append("uid", uid);

            if (panel_followStatus == "unfollow") {
                url = z.config('api').follow_user;
            } else {
                url = z.config('api').unfollow_user;
            }
            ajax(panel_follow, url, fd, this);
        });
    }

    window.panel_follow = function (data, obj) {

        if (data.isOk) {
            if (data.getItem('is_following')) {
                obj.setAttribute('class', 'followed');
                obj.setAttribute('data-status', 'followed');
            } else {
                obj.setAttribute('class', 'unfollow');
                obj.setAttribute('data-status', 'unfollow');
            }

            if (obj.getAttribute('data-uid') == dst_uid) {
                show(data);
            } else {
                var following_count = parseInt($following_count.html(this), 10) - 1;
                $following_count.html(following_count > 0 ? following_count : 0);
            }
        }
    }

    function getCommonUserSet() {

        current_uid = z.config('current_uid');
        if (current_uid == 0) {
            return;
        }
        var fd = new window.FormData();
        var url = z.config('api').follow_common_users;
        fd.append("dst_uid", dst_uid);
        fd.append("_token", config.token);
        net.ajax({
            dataType: 'json',
            url: url,
            method: 'post',
            withCredentials: true
        }).done(function (datas) {
            var data = z.pack(datas);
            if (data.isOk) {
                if (data.getItem('common_count') > 0) {
                    $user_info.removeClass('hide');
                    s('#common-user-count').html(data.getItem('common_count'));
                    s('#common-user').html(data.getItem('common_users').join(''));
                    return;
                }
                $user_info.addClass('hide');

            }

        }).send(fd);
    }


    $user_container.on('scroll', function () {
        var scrollHeight = this.scrollTop;
        var documentHeight = this.scrollHeight;

        if (scrollHeight == 0) {
            return;
        }
        if (loading_able && scrollHeight + 420 > documentHeight) {
            var url = z.config('api')[$user_container.attr('data-url')];
            var fd = new window.FormData();
            if (eid) {
                fd.append('eid', eid);
            }
            if ($user_follow.length > 0) {
                fd.append('uid', dst_uid);
            }
            fd.append('page', page);
            ajax(showPanel, url, fd, true);
            loading_able = false;
        }
    });
    // start
    z.ready(function () {
        if (s('#common-user').length > 0) {
            getCommonUserSet();
        }
        user_panel.append($user_container);

        trans_arr = {
            'trans_followed_user': z.trans('followed-user'),
            'trans_cancel_follow': z.trans('cancel-follow-user'),
            'trans_following_user': z.trans('following-user')
        };
    });
});
