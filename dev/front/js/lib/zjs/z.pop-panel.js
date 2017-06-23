define ([
    'zjs/z',
    'zjs/z.selector'
], function (z, s) {

    function PopPanel(opts) {
        var self = this;
        opts = opts || {};

        this.$panel = s.create('div').addClass('pop-panel');
        this.$panel_body = s.create('div').addClass('panel-body');
        this.initLayoutWrapper();
        this.$layout_mask.append(this.$panel);
        this.$panel.append(this.$panel_body);
        this.$panel.hide();

        this.hide_callback = opts.hide || false;
        this.show_callback = opts.show || false;
        this.is_hidding = true;

        if (opts.panel_class) {
            this.$panel.addClass(opts.panel_class);
        }

        this.$layout_mask.on('click', function (e) {
                    if (e.target === this) {
                        self.hide();
                    }
                }, true);

    }

    z.extend(PopPanel.prototype, {
        initLayoutWrapper: function () {
            var $wrapper = s('.pop-layout-wrapper'),
                $mask = s('.pop-layout-mask'),
                $loading = s('.pop-loading'),
                $container;
            if ($wrapper.length <= 0) {
                $wrapper = s.create('div').addClass('pop-layout-wrapper');
                $container = s(window.document.body);
                $container.append($wrapper);
            }
            if ($mask.length <= 0) {
                $mask = s.create('div').addClass('pop-layout-mask');
                $wrapper.append($mask);
            }
            if ($loading.length <= 0) {
                $loading = s.create('div').addClass('pop-loading');
                $loading.html('loading ...');
                $mask.append($loading);
            }
            this.$layout_wrapper = $wrapper;
            this.$layout_mask = $mask;
            this.$loading = $loading;
        },
        onShow: function (callback) {
            this.show_callback = callback;
            return this;
        },
        show: function () {
            if (!this.is_hidding) {
                return;
            }

            this.$layout_wrapper.show();
            this.$panel.show();
            this.$loading.hide();
            this.$panel.style('marginLeft', ( - this.$panel.prop('offsetWidth') / 2) + 'px');
            s(window.document.body).style('overflow-y', 'hidden');

            if (this.show_callback) {
                this.show_callback.call(this);
            }

            this.is_hidding = false;
        },
        onHide: function (callback) {
            this.hide_callback = callback;
            return this;
        },
        hide: function () {
            if (this.is_hidding) {
                return;
            }

            this.$layout_wrapper.hide();
            this.$panel.hide();
            this.$loading.hide();
            s(window.document.body).style('overflow-y', 'visible');

            if (this.hide_callback) {
                this.hide_callback(this);
            }

            this.is_hidding = true;
        },
        loading: function () {
            this.$layout_wrapper.show();
            this.$loading.show();
            //this.$loading.style('marginLeft', ( - this.$loading.prop('offsetWidth') / 2) + 'px');
            s(window.document.body).style('overflow-y', 'hidden');
        },
        getPanelHeaderElm: function () {
            if (this.$panel_header) {
                return this.$panel_header;
            }
            this.$panel_header = s.create('div').addClass('panel-header');
            this.$panel.prepend(this.$panel_header);
            return this.$panel_header;
        },
        setPanelTitle: function (title) {
            this.getPanelHeaderElm().html(title);
        },
        append: function (elem) {
            this.$panel_body.append(elem);
            return this;
        },
        html: function (html) {
            this.$panel_body.html(html);
            return this;
        }
    });

    return function (opts) {
        return new PopPanel(opts);
    };

});
