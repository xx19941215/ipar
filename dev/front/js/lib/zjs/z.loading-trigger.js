define([
    './z',
    './z.selector'
], function (z, s) {
    function LoadingTrigger(selector, opts) {
        var $more = s(selector),
            opts = opts || {};

        this.callback = null;
        this.$more = $more;
        this.more = $more[0];

        this.init();
    }

    z.extend(LoadingTrigger.prototype, {
        init: function() {
            var more = this.more,
                self = this;

            z.addEvent(window, 'scroll', function () {
                if (!z.isOffScreen(more)) {
                    self.callback.apply(self);
                }
            });
        },
        loading: function(callback) {
            this.callback = callback;
        }
    });

    return function (selector, opts) {
        return new LoadingTrigger(selector, opts);
    }
});
