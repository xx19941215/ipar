define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.simple-template-engine',
    'module/suggest'
], function (z, s, net, tpl, Suggest) {
    'use strict';

    function SuggestRecommend(opts) {
        opts = opts || {};

        this.$input = opts.$input;
        this.$search_result = opts.$search_result;

        this.filter = opts.filter || 'rqt';
        this.onRecommend = opts.onRecommend || '';

        this.init();
    }

    SuggestRecommend.prototype = {
        init: function () {
            var self = this;
            (new Suggest).init({
                $input: self.$input,
                filter: self.filter,
                onWaiting: function () {
                    //self.$search_result.addClass('hide');
                },
                onNoQuery: function () {
                    self.$search_result.addClass('hide');
                },
                onLoad: function (pack) {
                    if (!pack.isOk()) {
                        console.log(pack);
                        return;
                    }
                    self.$search_result.addClass('hide');
                    self.$search_result.html('');
                    self.loadSearchResult('rqt', pack.getItem('rqts'));
                    self.loadSearchResult('product', pack.getItem('products'));
                    self.loadSearchResult('feature', pack.getItem('features'));
                }
            });
        },
        loadSearchResult: function (type, entities) {
            var $search_result = this.$search_result,
                i, entity, list = [],
                len = entities.length,
                self = this;

            if (entities.length <= 0) {
                //$search_result.addClass('hide');
                return;
            }
            $search_result.removeClass('hide');

            for (i = 0; i < len; i++) {
                entity = entities[i];
                list.push('<a href="javascript:;" data-eid="' + entity.eid+ '" data-type="' + type + '">' + entity.title + '</a>');
            }
            if (list.length > 0) {
                $search_result.html('<ul><li>' + list.join('</li><li>') + '</li></ul>');
                $search_result.removeClass('hide');
                $search_result.find('a').on('click', function () {
                    net.routePost('fetch', {eid: s(this).attr('data-eid')}, function (pack) {
                        var entity;
                        if (!pack.isOk()) {
                            console.log('fetch entity failed');
                            return;
                        }
                        entity = pack.getItem('entity');
                        self.onRecommend.call(self, entity);
                        /*
                        self.getRecommendForm().find('.title').html(entity.title);
                        self.getRecommendForm().find('.content').html(entity.content);
                        self.getRecommendForm().find('input[name="dst_eid"]').prop('value', entity.eid);
                        load_zimg(self.getRecommendForm());
                        */
                    });
                    //console.log(s(this).attr('data-eid'));
                    //console.log(s(this).attr('data-type'));
                });
            }
        },
    };

    return SuggestRecommend;
});
