define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
], function (z, s, net, entity_module) {
    'use strict';

    function Suggest() {
        this.is_waiting = false;
    }

    Suggest.prototype = {
        init: function (opts) {
            var self = this,
                $input;

            opts = opts || {};

            this.$input = opts.$input;
            this.onWaiting = opts.onWaiting;
            this.onNoQuery = opts.onNoQuery;
            this.onLoad = opts.onLoad;
            this.onFocus = opts.onFocus || false;
            this.filter = opts.filter || 'rqt,product,article,user,company';

            $input = this.$input;

            $input
                .on('focus', function (e) {
                    if (self.onFocus) {
                        self.onFocus.call(self, e);
                    }
                    self.lookup();
                })
                .on('keyup', function () {
                    self.lookup();
                })
                .on('keydown', function () {
                    self.lookup();
                });
        },
        lookup: function () {
            var self = this,
                query = self.$input.prop('value');

            if (self.is_waiting) {
                return;
            }

            self.is_waiting = true;

            if (!query) {
                net.routePost(
                    'suggest',
                    {query: '', filter: self.filter},
                    function (pack) {
                        self.is_waiting = false;
                        self.onLoad.call(self, pack);
                    }
                );
                self.is_waiting = false;
                self.onNoQuery.call(self);
                return;
            }


            self.onWaiting.call(self);

            setTimeout(function () {
                query = self.$input.prop('value');
                if (!query) {
                    self.is_waiting = false;
                    return;
                }
                net.routePost(
                    'suggest',
                    {query: query, filter: self.filter},
                    function (pack) {
                        self.is_waiting = false;
                        self.onLoad.call(self, pack);
                    }
                );
            }, 500);
        }
    };

    return Suggest;
});
