define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.loading-trigger',
    'zjs/z.simple-template-engine',
    'module/comment',
    'module/vote',
    'module/like',
    'module/controller-util',
    'module/loading-status',
    'module/suggest-search'
], function (z, s, net, trigger, tpl, comment, vote, like, controller_util, loading_status, RqtController) {
    'use strict';

    var is_loading = false,
        rqt_controller,
        product_controller,
        idea_controller,
        feature_controller,
        maps = {
            story: {
                tpl: require('tpl/story.tpl'),
                set: 'story'
            },
            rqt: {
                tpl: require('tpl/rqt.tpl'),
                set: 'story'
            },
            product: {
                tpl: require('tpl/box.tpl'),
                set: 'product'
            },
            article: {
                tpl: require('tpl/article.tpl'),
                set: 'article'
            },
            group: {
                tpl: require('tpl/group.tpl'),
                set: 'box'
            },
            search: {
                tpl: require('tpl/entity-search.tpl'),
                set: 'entity'
            },
            search_article: {
                tpl: require('tpl/article.tpl'),
                set: 'article'
            },
            search_rqt: {
                tpl: require('tpl/entity.tpl'),
                set: 'entity',
                //todo: wrap 用于指定插入页面的div位置, set 虽然同时指定网页中对应位置,但是 set 还需要指定 接受 数据 的 item;
                wrap: 'rqt'
            },
            search_product: {
                tpl: require('tpl/box.tpl'),
                set: 'entity',
                wrap: 'product'
            },
            search_user: {
                tpl: require('tpl/user.tpl'),
                set: 'user'
            },
            search_company: {
                tpl: require('tpl/company.tpl'),
                set: 'company'
                // wrap: 'company'
            },
            solution: {
                tpl: require('tpl/solution.tpl'),
                set: 'solution'
            },
            property: {
                tpl: require('tpl/property.tpl'),
                set: 'property'
            },
            feature_product: {
                route: 'feature_product',
                tpl: require('tpl/entity.tpl'),
                set: 'product'
            },
            tag_rqt: {
                tpl: require('tpl/entity.tpl'),
                set: 'entity'
            },
            tag_product: {
                tpl: require('tpl/box.tpl'),
                set: 'product'
            },
            tag_article: {
                tpl: require('tpl/article.tpl'),
                set: 'article'
            },
            company_product: {
                tpl: require('tpl/box.tpl'),
                set: 'product'
            },
            brand_tag_product: {
                tpl: require('tpl/box.tpl'),
                set: 'product'
            }
        };

    function init() {
        var $loading = s('.loading');


        if ($loading.length <= 0) {
            return;
        }

        //为了解决一个页面 有多个加载项
        //route 和 loadding 都需要循环
        for (var i = 0; i < $loading.length; i++) {
            var elem = $loading[i],
                $elem = s(elem);

            loading($elem, dispatch(elem));

            if (elem.is_inited) {
                continue;
            }

            trigger($elem).loading(function () {
                if (!is_loading && is_large_screen()) {
                    loading(this.$more, dispatch(this.more));
                }
            });

            $elem.find('.trigger').on('click', function () {
                var $current_loading = s(this).parentByClasses('loading');
                loading($current_loading, dispatch($current_loading[0]));
            });

            elem.is_inited = true;
        }

    }

    function is_large_screen() {
        var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        return (width >= 1024);
    }

    function loading($loading, route) {
        var old_action = route.action;
        var action = route.action,
            page = parseInt(route.params.page || 1),
            $loading = s($loading),
            map = maps[action];

        loading_status.loadingStatusSet(route.action, [1, 0, 0]);

        if (map.route) {
            route.action = map.route;
            action = route.action;
        }

        if (!map) {
            return;
        }

        page = isNaN(page) ? 1 : page;
        is_loading = true;


        /*
         net.ajax({
         dataType: 'json',
         url: z.api_url(action, route.params),
         method: 'get',
         withCredentials: true
         }).done(function(data) {
         */
        net.routeGet(action, route.params, function (pack) {

            var arr = [],
                i,
                set = map.set,
                $set,
                $wrap,
                data;

            if (!pack.isOk()) {
                return;
            }

            var has_result = 0;

            data = pack.getItem(set);

            //has_responsed
            var has_responsed = 1;
            loading_status.loadingStatusSet(route.action, [1, has_responsed, 0])

            //如果loading得到数据，则隐藏当前loading class的头部样式
            var route_action = route.action;
            if (data[0]) {
                has_result = 1;
                if (route_action == 'search_product' || route_action == 'tag_product') {
                    s('.product-header-div').removeClass('hide');
                    s('.product-hide').removeClass('hide');
                } else if (route_action == 'search_user') {
                    s('.user-header-div').removeClass('hide');
                    s('.user-hide').removeClass('hide');
                } else if (route_action == 'search_rqt' || route_action == 'tag_rqt') {
                    s('.rqt-header-div').removeClass('hide');
                    s('.rqt-hide').removeClass('hide');
                } else if (route_action == 'search_article' || route_action == 'tag_article') {
                    s('.article-header-div').removeClass('hide');
                    s('.article-hide').removeClass('hide');
                } else if (route_action == 'search_company') {
                    s('.company-header-div').removeClass('hide');
                    s('.company-hide').removeClass('hide');
                }
                loading_status.loadingStatusSet(route.action, [1, 1, has_result]);
            } else if (route_action == 'search_product') {
                s('.product-hide').addClass('hide');
            } else if (route_action == 'search_user') {
                s('.user-hide').addClass('hide');
            } else if (route_action == 'search_rqt') {
                s('.rqt-hide').addClass('hide');
            } else if (route_action == 'search_article') {
                s('.article-hide').addClass('hide');
            } else if (route_action == 'search_company') {
                s('.company-hide').addClass('hide');
            }

            res = loading_status.checkAllResultStatusIsNull();

            var i, content_asso = '', content_hot = '', url, btn_prefix;

            if (res == 1) {
                if (route.action.indexOf('search') != -1) {
                    net.getPack('ipar-rest-search-associate-suggest', {'query': route.params.query}, function (data) {
                        if (data.ok && data.items.associate_suggest[0] && data.items.associate_suggest[0].title) {
                            for (i in data.items.associate_suggest) {
                                url = data.items.associate_suggest[i].url;
                                content_asso += '<a href="' + url + '" >' + data.items.associate_suggest[i].title + '</a> ';
                            }
                            content_asso = z.trans('do-you-mean') + '<br/><div class="asso_suggest">' + content_asso + '</div>';
                            for (i in data.items.hot_suggest) {
                                url = data.items.hot_suggest[i].url;
                                content_hot += '<a href="' + url + '" >' + data.items.hot_suggest[i].title + '</a> ';
                            }
                            content_hot = z.trans('everybody-is-searching') + '<br/><div class="hot_suggest">' + content_hot + '</div>';
                            btn_prefix = "<div class='commit_product_rqt'><div>" + z.trans('no-search-result-about') + '"' + route.params.query
                                + '"</div><button class=submit_rqt>' + z.trans('submit-rqt') + '</button><button class=submit_product>' + z.trans('submit-product') + '</button></div>';
                            s('.sorry-no-result').html(btn_prefix + content_asso + '<br/>' + content_hot);
                            //banding
                            s('.submit_rqt').on('click', function () {
                                RqtController.get_rqt_controller().showPanel({
                                    panel_title: z.trans('create-rqt'),
                                    submit_route: 'rqt_save'
                                });
                            });

                            s('.submit_product').on('click', function () {
                                RqtController.get_rqt_controller().showPanel({
                                    panel_title: z.trans('create-product'),
                                    submit_route: 'product_save'
                                });
                            });
                        }
                    });
                }

                s('.no-search-result').removeClass('hide');
                s('.sorry-no-result').removeClass("hide");
                s('.search-loading').addClass('hide');
            } else {
                s('.no-search-result').addClass('hide');
                s('.sorry-no-result').addClass("hide");
            }


            var res = loading_status.checkAllStatusIsLoading();
            if (res == 0) {
                s('.search-loading').removeClass('hide');
            } else {
                s('.search-loading').addClass('hide');
            }

            if (!data || data.length <= 0) {
                $loading.addClass('completed');
                return;
            }

            res = loading_status.checkOneStatusIsLoading();

            if (res == 0) {
                if (data && data.length > 0) {
                    s('.no-search-result').addClass('hide');
                    s('.sorry-no-result').addClass("hide");
                } else {
                    s('.no-search-result').removeClass('hide');
                    s('.sorry-no-result').removeClass("hide");
                }
            }

            $wrap = s('.' + set + '-set-wrap');

            if (map.wrap) {
                $wrap = s('.' + map.wrap + '-set-wrap');
            }

            if ($wrap.length <= 0) {
                return;
            }

            $set = s.create('div');
            $set.prop('id', set + '-set-' + page);
            $set.addClass(set + '-set');

            for (i in data) {
                data[i].trans_comment = z.trans('comment');
                arr.push(tpl(map.tpl, data[i]));
            }
            $set.html(arr.join(''));
            $wrap.append($set);


            var i, isVoted, data_id, this_item_node_name,
                this_item_node, appreciate_btn, vote_count_node;

            if (action == 'property') {
                for (i = 0; i < pack.items.property.length; i++) {
                    isVoted = pack.items.property[i].is_voted;
                    data_id = pack.items.property[i].id;
                    this_item_node_name = '.property-id-' + data_id;
                    this_item_node = s(this_item_node_name);
                    appreciate_btn = this_item_node[0].children[0].children[0];
                    vote_count_node = this_item_node[0].children[1];
                    if (isVoted == 'voted') {
                        appreciate_btn.className += 'voted';
                    }
                }
            } else if (action == 'solution') {
                for (i = 0; i < pack.items.solution.length; i++) {
                    isVoted = pack.items.solution[i].is_voted;
                    data_id = pack.items.solution[i].id;
                    this_item_node_name = '.property-id-' + data_id;
                    this_item_node = s(this_item_node_name);
                    appreciate_btn = this_item_node[0].children[0].children[0];
                    vote_count_node = this_item_node[0].children[1];
                    if (isVoted == 'voted') {
                        appreciate_btn.className += 'voted';
                    }
                }
            }

            page++;
            $loading.attr('data-page', page);
            route.params.page = page;

            entity($set);
            like.init($set);
            comment.init($set);

            // 如果当前要加载的页面URL出现“search?”或/tag/,限制加载
            var continue_loading = 0;
            if ($loading[0].baseURI.indexOf('search?') > 0 || $loading[0].baseURI.substr(-18, 5) == '/tag/') {
                continue_loading = 1;
            }
            if (continue_loading) {
                return;
            }

            if (z.isOffScreen($loading[0])) {
                is_loading = false;
            } else {
                loading($loading, route);
            }
            vote();

        });
        route.action = old_action;
    }

    function dispatch(elem) {
        var attrs = elem.attributes,
            len = attrs.length,
            i = 0,
            attr,
            name,
            key,
            route = {
                action: '',
                params: {}
            };

        for (i = 0; i < len; i++) {
            attr = attrs[i];
            name = attr.nodeName;
            if (name.substring(0, 5) == 'data-') {
                key = name.substring(5);
                if (key == 'action') {
                    route.action = attr.nodeValue;
                } else {
                    route.params[key] = attr.nodeValue;
                }
            }
        }

        return route;
    }

    function entity(selector) {
        var $ctn;
        selector = selector || 'body';
        $ctn = s(selector);

        function show_content() {
            var $entity = s(this).parentByClasses('entity');
            $entity.find('.entity-content').show();
            $entity.find('.entity-abbr').hide();
            $entity.find('.entity-imgs').hide();
            zimg($entity);
        }

        $ctn.find('.entity-imgs img').on('click', show_content);
        $ctn.find('.entity-abbr .read-more').on('click', show_content);

    }

    function zimg(selector) {
        var items;
        selector = selector || 'body';
        items = s(selector).find('.zimg');
        items.each(function (item) {
            var $item = s(item),
                protocol = $item.attr('data-protocol') || 'http://',
                site = $item.attr('data-site') || 'static',
                host = z.config('site')[site].host,
                dir = $item.attr('data-dir'),
                name = $item.attr('data-name'),
                ext = $item.attr('data-ext');

            $item.attr('src', protocol + host + dir + '/' + name + '.' + ext);
        });
    }

    function moduleLoad() {
        if (s('.keyword').length > 0) {
            require.ensure([], function () {
                require('module/area');
                require('module/company-logo');
            });
        }
        if (s('.company').length > 0) {
            require.ensure([], function () {
                require('module/group');
            });
        }

    };

    function entity_edit(type) {
        if (!z.config('current_uid')) {
            return;
        }

        function handle_entity_edit($entity) {
            var type = $entity.attr('data-type_key'),
                title,
                controller;

            if (type == 'rqt') {
                controller = controller_util.get_rqt_controller();
                //controller.setRoute('rqt_save');
            } else if (type == 'product') {
                controller = controller_util.get_product_controller();
                //controller.setRoute('product_save');
            } else if (type == 'idea') {
                controller = controller_util.get_idea_controller();
                //controller.setRoute('idea_save');
            } else if (type == 'feature') {
                controller = controller_util.get_feature_controller();
                //controller.setRoute('feature_save');
            }

            title = '';
            if ($entity.find('.entity-title a').length > 0) {
                title = $entity.find('.entity-title a').html().trim();
            }
            controller.showPanel({
                panel_title: z.trans('eidt-' + type),
                submit_route: type + '_save',
                eid: $entity.attr('data-eid'),
                title: title,
                content: ($entity.find('.entity-content').html() || '').trim()
            });
            controller.onSubmit = function (pack) {
                if (pack.isOk()) {
                    $entity.addClass('highlight');
                    $entity.find('.entity-title a').html(pack.getItem('title'));
                    $entity.find('.entity-content').show().html(pack.getItem('content'));
                    $entity.find('.entity-abbr').hide();
                    $entity.find('.entity-imgs').hide();
                }
            };
        }

        s('div.entity').each(function (elem) {
            var $entity = s(elem),
                $entity_edit = $entity.find('.entity-edit');
            $entity_edit.on('click', function () {
                handle_entity_edit($entity);
            });
        });

    }

    function a_touchend()
    {
         s('a').on('touchend', function(e){
            var link = s(this).attr('href');
            window.location = link;
         });
    }

    window.untrans = function (str) {
        window.collector = window.collector || {};
        if (window.collector[str]) {
            return;
        }
        window.collector[str] = str;
        net.routePost('js_trans', {'key': str}, function (data) {
        });
    }
    // start
    z.ready(function () {
        init();
        zimg();
        entity();
        comment.init();
        moduleLoad();
        entity_edit();
        a_touchend();
    });

    return {
        init: init
    };
});
