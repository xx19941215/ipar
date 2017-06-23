define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.pop-panel',
    'zjs/z.simple-template-engine'
], function (z, s, net, panel, tpl) {
    'use strict';

    var $tag_edit = s('.tag-edit'),
        $tag_dst_list,
        dst_type,
        dst_id,
        current_page = 1,
        tag_panel;

    function show_tag_list() {
        var fd = new window.FormData();
        fd.append('page', current_page);
        fd.append('dst_type', dst_type);
        fd.append('dst_id', dst_id);
        ajax('tag_list', fd, function (data) {
            var pack = z.pack(data),
                i,
                list,
                stack = [],
                row,
                html = require('tpl/tag-dst-row.tpl');
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
                row = list[i];
                append_tag_dst_row(row);
                //stack.push(tpl(html, list[i]));
            }
            current_page += 1;
            /*
            if (is_at_buttom()) {
                show_tag_list();
            }
            */
            //s('.tag-dst-list').html(stack.join(''));
        });
    }

    /*
    function is_at_buttom() {
        var list = $tag_dst_list[0],
            t = list.scrollTop,
            sh = list.scrollHeight,
            ch = list.clientHeight;

            console.log(sh - ch -t);
        return (sh - ch - t <= 5);
    }
    */

    function append_tag_dst_row(row) {
        var html = require('tpl/tag-dst-row.tpl'),
            //$tag_dst_list = s('.tag-dst-list'),
            $item;

        row.vote_text = z.trans('votes');

        function load_vote_users(data) {
            var pack = z.pack(data),
                vote_users;

            if (pack.isOk()) {
                vote_users = pack.getItem('vote_users');

                $item.find('.vote-user-wrap').removeClass('hide').html(vote_users.join(''));
                $item.find('.vote-count').html(pack.getItem('vote_count'));
                if (vote_users.length <= 0) {
                    $item.find('.vote-user-wrap').addClass('hide');
                }
            }
        }

        if (s('#tag-dst-row-' + row.tag_dst_id).length == 1) {
            return;
        }

        $item = s.create('div');
        $item.addClass('item clearfix');
        $item.html(tpl(html, row));
        $tag_dst_list.append($item);

        $item.find('.tag-vote').on('click', function () {
            var fd = new window.FormData();
            fd.append('tag_dst_id', row.tag_dst_id);
            ajax('tag_vote_users',  fd, function (data) {
                load_vote_users(data);
            });
        });

        $item.find('.tag-op').on('click', function () {
            var $self = s(this),
                fd = new window.FormData(),
                route;
            if ($self.hasClass('vote')) {
                route = 'tag_vote';
            } else {
                route = 'tag_unvote';
            }
            fd.append('tag_dst_id', row.tag_dst_id);
            ajax(route, fd, function (data) {
                load_vote_users(data);
                $self.removeClass('vote unvote');
                if (route == 'tag_vote') {
                    $self.addClass('unvote');
                } else {
                    $self.addClass('vote');
                }
            });
        });
    }

    function init_tag_dst_form() {
        s('.tag-dst-form').on('submit', function () {
            var fd = new window.FormData(this);
            fd.append('dst_type', dst_type);
            fd.append('dst_id', dst_id);
            ajax('tag_save', fd, function (data) {
                var pack = z.pack(data);
                if (pack.isOk()) {
                    append_tag_dst_row(pack.getItem('tag_dst'));
                    //s('.tag-dst-form .input').prop('value', '');
                }
            });
        });

        s('.tag-dst-form .input').on('keyup', function () {
            var $self = s(this),
                str = $self.prop('value'),
                $tag_dst_item = s('.tag-dst-list > .item');

            $tag_dst_item.each(function (item) {
                var $item = s(item),
                    $tag_title = $item.find('.tag-title'),
                    text = $tag_title.text();

                if (text.match(new RegExp(str, 'i'))) {
                    $item.show();
                } else {
                    $item.hide();
                }
                //console.log($tag_title.html());
                //console.log($tag_title);
            });
        });
    }

    function init_tag_dst_list() {
        if (!$tag_dst_list) {
            $tag_dst_list = s('.tag-dst-list');
        }
        /*
        $tag_dst_list.on('scroll', function () {
            if (is_at_buttom()) {
                show_tag_list();
            }
        });
        */
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

    if ($tag_edit.length > 0) {
        tag_panel = panel({panel_class: 'tag-panel'});
        tag_panel.html(
            tpl(
                require('tpl/tag-box.tpl'),
                {
                    submit: z.trans('submit')
                }
            )
        );
        tag_panel.onHide(function () {
            var fd = new window.FormData(),
                tpl_str = require('tpl/tag-hot-item.tpl');

            fd.append('dst_type', dst_type);
            fd.append('dst_id', dst_id);
            fd.append('cpp', 3);
            ajax('tag_list', fd, function (data) {
                var pack = z.pack(data),
                    arr = [];
                if (pack.isOk()) {
                    pack.getItem('list').forEach(function (item) {
                        arr.push(tpl(tpl_str, item));
                    });
                    if (arr.length > 0) {
                        s('.tag-hot-list').html(arr.join(''));
                    }
                }
            });
        });

        init_tag_dst_form();
        init_tag_dst_list();
    }

    $tag_edit.on('click', function () {
        var $self = s(this);
        dst_type = parseInt($self.attr('data-dst_type'));
        dst_id = parseInt($self.attr('data-dst_id'));
        tag_panel.show();
        show_tag_list();
    });

});
