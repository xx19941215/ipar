define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.pop-panel',
    'module/idea-form',
    'module/message'
], function (z, s, net, pop_panel, IdeaForm, Message) {
    'use strict';

    function IdeaController() {
        var self = this;
        this.panel = pop_panel({panel_class: 'form-wrap'});
        this.$submit_ctn = s.create('div').addClass('submit-ctn');

        this.panel.append(this.$submit_ctn);

        this.idea_form  = new IdeaForm({
            $ctn: this.$submit_ctn,
            onSubmit: function (pack) {
                self.panel.hide();
                if (self.onSubmit) {
                    self.onSubmit.call(self, pack);
                    return;
                }

                self.getMessage().addTopMessage(pack.getItem('html_message'));
                self.idea_form.clear();
            },
            onCancel: function () {
                self.panel.hide();
            }
        });
    }

    IdeaController.prototype = {
        getMessage: function () {
            if (this.message) {
                return this.message;
            }
            this.message = new Message();
            return this.message;
        },
        showPanel: function (opts) {
            if (opts.panel_title) this.panel.setPanelTitle(opts.panel_title);
            if (opts.submit_route) this.idea_form.setRoute(opts.submit_route);
            if (opts.eid) this.idea_form.setEid(opts.eid);
            if (opts.content) this.idea_form.setContent(opts.content);
            if (opts.rqt_eid) this.idea_form.setRqtEid(opts.rqt_eid);

            this.panel.show();
        }
    };

    return IdeaController;
});
