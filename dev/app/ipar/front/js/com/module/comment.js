define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.simple-template-engine',
    'module/auth'
], function (z, s, net, tpl, auth) {
    'use strict';

    var $entries,
        type,
        $entry_type = ['entity', 'article'];

    var entity_type = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

    var comment = {
        init: function ($set) {
            $set = $set || 'body';
            $set = s($set);
            init($set);
        }
    }

    var init = function ($set) {

        if (s('.entity').length) {
            $entries = s('.entity');
            type = 'entity';
        } else if (s('.article').length) {
            $entries = s('.article');
            type = 'article';
        } else {
            return;
        }

        if (!config.current_uid) s('.comment-form').hide();
        bindUiActions($set);
        s('.comment-form textarea').on('click', function () {
            s('.comment-form textarea').autosize();
        })
    }

    function bindUiActions($set) {
        $set.find('.comment-form').on('submit', function (e) {
            if (!auth.requireLogin()) {
                e.preventDefault();
                return false;
            }
            var $form = s(this),
                $button = $form.find('.submit-comment-button');

            z.cancelEvent(e);

            $button.attr('disabled', true);
            createComment(this);
            return false;
        });

        $set.find('.comments-list').on('scroll', function () {
            var t = this.scrollTop,
                sh = this.scrollHeight,
                ch = this.clientHeight,
                $entry = s(this).parentByClasses(type);
            if (t == 0) {
                this.scrollTop = 1;
                getEarlierComments($entry);
                // enabledLoading = true;
                return;
            }
            if (sh - ch - t == 0) {
                getLaterComments($entry);
            }
        });

        $set.find('.show-comments').on('click', function () {
            if (!auth.requireLogin()) {
                return;
            }
            var $entry = s(this).parentByClasses(type),
                $container = $entry.find('.comments-container');
            getLatestComments($entry);
            if ($container.isHidden() && $container.attr('data-comments-count') == 0 && !config.current_uid) return;
            foldComment($container, this);
            $container.find('.comment-textbox')[0].focus();
        });
    }

    //折叠评论
    function foldComment($container, node) {
        if (node.className.split(' ')[1] == 'has-fold') {
            $container.hide(node);
            node.className = node.className.replace(' has-fold', '');
        } else {
            $container.show(node);
            node.className += ' ' + 'has-fold';
        }
    }

    function createComment(form) {
        var fd = new FormData(form);
        fd.append('_token', z.get_token());
        var type_id = form.dst_type_id.value;

        if (type_id == 'article') {
            net.routePost('create_article_comment', fd, function (pack) {
                var $entry = s(form).parentByClasses(type),
                    comments_list = $entry.find('.comments-list')[0];

                if (!pack.isOk()) {
                    return console.log(pack.getErrors());
                }

                clearCommentForm(form);
                getLaterComments($entry);
                incrementCommentCount($entry);
                $entry.find('.submit-comment-button')[0].removeAttribute('disabled');
            });
        } else if (entity_type.indexOf(type_id)) {
            net.routePost('create_comment', fd, function (pack) {
                var $entry = s(form).parentByClasses(type),
                    comments_list = $entry.find('.comments-list')[0];

                if (!pack.isOk()) {
                    return console.log(pack.getErrors());
                }

                clearCommentForm(form);
                getLaterComments($entry);
                incrementCommentCount($entry);
                $entry.find('.submit-comment-button')[0].removeAttribute('disabled');
            });

        }

    }

    function registerDeleteButton($entry) {
        var $deleteBtn;
        if ($entry) {
            $deleteBtn = $entry.find('.delete-comment-button');
        } else {
            $deleteBtn = s('.delete-comment-button');
        }
        $deleteBtn.each(function (deleteBtn) {
            var $comment = s(deleteBtn).parentByClasses('comment');
            if ($comment.attr('data-uid') == config.current_uid) {
                deleteBtn.style.display = 'inline';
            }
        });
        s('.entity-unbind').on('click', function () {
            var $comment = s(this).parentByClasses('comment'),
                id = $comment.attr('data-id'),
                form = s(this).parent('.comment-form')[0];
            deleteComment(id, $comment);
        });
        s('.entity-unbind').removeClass('entity-unbind');
    }


    function registerReplyButton($entry) {
        var $replyBtn;
        if ($entry) {
            $replyBtn = $entry.find('.reply-comment-button');
        } else {
            $replyBtn = s('.reply-comment-button');
        }
        $replyBtn.on('click', function () {
            var $comment = s(this).parentByClasses('comment'),
                $entry = s(this).parentByClasses(type),
                commentId = $comment.attr('data-id'),
                commentUid = $comment.attr('data-uid'),
                $form = $entry.find('.comment-form'),
                replyInput = $form[0].reply_id,
                replyUidInput = $form[0].reply_uid;

            if (!replyInput) {
                replyInput = s.create('input');
                replyUidInput = s.create('input');
                updateFormValue();
                $form.append(replyInput).append(replyUidInput);
            } else {
                updateFormValue();
            }

            $form.find('.comment-textbox')[0].focus();

            registerReplyHint($comment);

            function updateFormValue() {
                s(replyInput).attr('type', 'hidden')
                    .attr('name', 'reply_id')
                    .attr('value', commentId);

                s(replyUidInput).attr('type', 'hidden')
                    .attr('name', 'reply_uid')
                    .attr('value', commentUid);
            }
        })
    }


    function registerReplyHint($comment) {
        var $entry = $comment.parentByClasses(type),
            $form = $entry.find('.comment-form'),
            $replyHint = $form.find('.reply-hint'),
            $formContent = s($form[0].content),
            replyUserNick = $comment.find('.user-nick').html(),
            replyHintWidth;

        if ($replyHint.length == 0) {
            $replyHint = s.create('span');
            $replyHint.addClass('reply-hint');
            $replyHint.html('reply to ' + replyUserNick);
            $form.append($replyHint);
            $formContent.on('keydown', function (evt) {
                var charcode = evt.which || evt.keyCode || evt.charCode,
                    $replyHint;
                if (charcode == 8) {
                    if (!$formContent.prop('value')) {
                        $form = s(evt.target).parentByClasses(type).find('.comment-form');
                        if ($form[0].reply_id) return removeReplyHint($form);
                    }
                }
            });

        } else {
            $replyHint.html('reply to ' + replyUserNick);
        }

        replyHintWidth = $replyHint[0].offsetWidth;

        $form[0].content.style.paddingLeft = replyHintWidth + 16 + 'px';

        function removeReplyHint($form) {
            var $replyHint = $form.find('.reply-hint');
            $replyHint.remove();
            $form[0].content.style.paddingLeft = 16 + 'px';
            $form[0].reply_id.remove();
            $form[0].reply_uid.remove();
        }

    }

    function deleteComment(id, $comment) {
        var fd = new FormData();
        fd.append('id', id);

        var $entry = $comment.parentByClasses(type);
        if ($entry.attr('data-type') == 'article') {
            net.routePost('delete_article_comment', fd, function (data) {
                if (data.ok != 1) return console.log('err delete comment');
                $comment.remove();
                decrementCommentCount($entry);
            })
        } else if (entity_type.indexOf($entry.attr('data-type'))) {
            console.log('data-type');
            net.routePost('delete_comment', fd, function (data) {
                if (data.ok != 1) return console.log('err delete comment');
                $comment.remove();
                decrementCommentCount($entry);
            })
        }
    }

    function getLatestComments($entry) {
        var fd = new FormData();
        if ($entry.attr('data-type') == 'article') {
            fd.append('article_id', $entry.attr('data-id'));
            fd.append('_token', z.get_token());

            net.ajax({
                dataType: 'json',
                url: config.api.get_latest_article_comments,
                method: 'post',
                withCredentials: true
            }).done(function (data) {
                var rendered_comments = renderComments(data),
                    comments_list = $entry.find('.comments-list')[0],
                    count = $entry.find('.comments-container').attr('data-comments-count');
                if (!rendered_comments) {
                    if (!config.current_uid) $entry.find('.comments-container').hide();
                    markDataComplete($entry);
                    return;
                }
                if (rendered_comments.length == count) markDataComplete($entry);
                rendered_comments && s(comments_list).html(rendered_comments.reverse().join(''));
                comments_list.scrollTop = 1000;
                registerDeleteButton($entry);
                registerReplyButton();
                renderReplyInfo($entry);

            }).send(fd);
        } else if (entity_type.indexOf($entry.attr('data-type'))) {
            fd.append('dst_type', $entry.attr('data-type'));

            fd.append('dst_id', $entry.attr('data-id'));
            fd.append('_token', z.get_token());

            net.ajax({
                dataType: 'json',
                url: config.api.get_latest_comments,
                method: 'post',
                withCredentials: true
            }).done(function (data) {
                var rendered_comments = renderComments(data),
                    comments_list = $entry.find('.comments-list')[0],
                    count = $entry.find('.comments-container').attr('data-comments-count');
                if (!rendered_comments) {
                    if (!config.current_uid) $entry.find('.comments-container').hide();
                    markDataComplete($entry);
                    return;
                }
                if (rendered_comments.length == count) markDataComplete($entry);
                rendered_comments && s(comments_list).html(rendered_comments.reverse().join(''));
                comments_list.scrollTop = 1000;

                registerDeleteButton($entry);
                registerReplyButton();
                renderReplyInfo($entry);

            }).send(fd);
        }

    }

    function getEarlierComments($entry) {
        var fd = new FormData(),
            $container = $entry.find('.comments-container');

        if ($container.hasClass('complete')) return console.log('no earlier comments, abort');

        if ($entry.attr('data-type') == 'article') {
            fd.append('article_id', $entry.attr('data-id'));
            fd.append('oldest_id', getEdgeId($entry, Math.min));
            fd.append('_token', z.get_token());

            net.ajax({
                dataType: 'json',
                url: config.api.get_earlier_article_comments,
                method: 'post',
                withCredentials: true
            }).done(function (data) {
                var rendered_comments = renderComments(data);
                if (!rendered_comments) {
                    markDataComplete($entry);
                    return;
                }
                ;

                var comments_elem = $entry.find('.comments-list')[0];
                rendered_comments.forEach(function (comment_html) {
                    var comment_node = createElemFromStr(comment_html);
                    s(comments_elem).prepend(comment_node);
                });

                registerDeleteButton($entry);

                registerReplyButton($entry);
                renderReplyInfo($entry);

                comments_elem.scrollTop = 5;

            }).send(fd);
        } else if (entity_type.indexOf($entry.attr('data-type'))) {
            fd.append('dst_type', $entry.attr('data-type'));
            fd.append('dst_id', $entry.attr('data-id'));
            fd.append('oldest_id', getEdgeId($entry, Math.min));
            fd.append('_token', z.get_token());

            net.ajax({
                dataType: 'json',
                url: config.api.get_earlier_comments,
                method: 'post',
                withCredentials: true
            }).done(function (data) {
                var rendered_comments = renderComments(data);
                if (!rendered_comments) {
                    markDataComplete($entry);
                    return;
                }
                ;

                var comments_elem = $entry.find('.comments-list')[0];
                rendered_comments.forEach(function (comment_html) {
                    var comment_node = createElemFromStr(comment_html);
                    s(comments_elem).prepend(comment_node);
                });

                registerDeleteButton($entry);

                registerReplyButton($entry);
                renderReplyInfo($entry);

                comments_elem.scrollTop = 5;

            }).send(fd);
        }

    }

    function getLaterComments($entry) {
        var fd = new FormData();

        if ($entry.attr('data-type') == 'article') {
            fd.append('article_id', $entry.attr('data-id'));
            fd.append('latest_id', getEdgeId($entry, Math.max));
            fd.append('_token', z.get_token());

            net.ajax({
                dataType: 'json',
                url: config.api.get_later_article_comments,
                method: 'post',
                withCredentials: true
            }).done(function (data) {
                var rendered_comments = renderComments(data);
                if (!rendered_comments) return;

                var comments_elem = $entry.find('.comments-list')[0];
                rendered_comments.forEach(function (comment_html) {
                    var comment_node = createElemFromStr(comment_html);
                    s(comments_elem).append(comment_node);
                });

                registerDeleteButton();

                registerReplyButton();
                renderReplyInfo($entry);

                comments_elem.scrollTop = comments_elem.scrollHeight - comments_elem.clientHeight;
            }).send(fd);
        } else if (entity_type.indexOf($entry.attr('data-type'))) {
            fd.append('dst_type', $entry.attr('data-type'));
            fd.append('dst_id', $entry.attr('data-id'));
            fd.append('latest_id', getEdgeId($entry, Math.max));
            fd.append('_token', z.get_token());

            net.ajax({
                dataType: 'json',
                url: config.api.get_later_comments,
                method: 'post',
                withCredentials: true
            }).done(function (data) {
                var rendered_comments = renderComments(data);
                if (!rendered_comments) return;

                var comments_elem = $entry.find('.comments-list')[0];
                rendered_comments.forEach(function (comment_html) {
                    var comment_node = createElemFromStr(comment_html);
                    s(comments_elem).append(comment_node);
                });

                registerDeleteButton();

                registerReplyButton();
                renderReplyInfo($entry);

                comments_elem.scrollTop = comments_elem.scrollHeight - comments_elem.clientHeight;
            }).send(fd);
        }
    }

    function renderComments(data) {
        var comment_tpl = require('tpl/comment.tpl'),
            rendered_comments = [],
            raw_comments = data.items.comments;
        if (raw_comments.length == 0) {
            console.log('no more comment');
            return;
        };

        for (var i in raw_comments) {
            raw_comments[i].trans_comment = z.trans('comment');
            raw_comments[i].trans_delete = z.trans('delete');
            raw_comments[i].trans_reply = z.trans('reply');
            var html = tpl(comment_tpl, raw_comments[i]);
            rendered_comments.push(html);
        }
        return rendered_comments;
    }

    function renderReplyInfo($entry) {
        var $comments = $entry.find('.comments-list .comment');
        $comments.each(function (comment) {
            var $comment = s(comment),
                replyUserNick = $comment.attr('data-reply-user-nick'),
                $replyUserNick,
                $replyText;
            if (replyUserNick) {
                $replyUserNick = $comment.find('.reply-user-nick');
                $replyText = $comment.find('.reply-text');
                $replyUserNick.html(replyUserNick);
                $replyText.style('display', 'inline');
            }
        })
    }

    function getEdgeId($entry, f) {
        var ids = [];
        $entry.find('.comments-list .comment').each(function (comment) {
            ids.push(parseInt(s(comment).attr('data-id'), 10));
        });
        return f.apply(null, ids);
    }

    function createElemFromStr(str) {
        var p = document.createElement('p');
        p.innerHTML = str;
        return p.childNodes[0];
    }

    function clearCommentForm(form) {
        form.content.value = '';
    }

    function handleEarlierButton($entry) {
        var count = $entry.attr('data-comments-count'),
            comments_elem = $entry.find('.comments-list');
        if (count > comments_elem.length) return;
        $entry.find('.comments-container').addClass('complete');
    }

    function markDataComplete($entry) {
        $entry.find('.comments-container').addClass('complete');
    }

    function incrementCommentCount($entry) {
        var $container = $entry.find('.comments-container'),
            count = parseInt($entry.find('.comments-container').attr('data-comments-count'), 10);

        $container.attr('data-comments-count', count + 1);
        $entry.find('.comments-count').html(count + 1);
    }

    function decrementCommentCount($entry) {
        var $container = $entry.find('.comments-container'),
            count = parseInt($entry.find('.comments-container').attr('data-comments-count'), 10);

        $container.attr('data-comments-count', count - 1);
        $entry.find('.comments-count').html(count - 1);
    }

    return comment;

});
