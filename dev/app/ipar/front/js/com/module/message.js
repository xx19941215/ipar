define([
    'zjs/z',
    'zjs/z.selector',
], function(z, s) {
    'use strict';

    function Message() {
    }

    Message.prototype = {
        addTopMessage: function (msg) {
            var $msg = s.create('div').addClass('item');
            $msg.html(msg);
            this.getTopCtnElem().append($msg);
            this.getTopCtnElem().removeClass('hide');
        },
        getTopCtnElem: function () {
            var self = this;
            if (this.$top_ctn) {
                return this.$top_ctn;
            }
            this.$top_ctn = s.create('div').addClass('msg-top-ctn hide');
            this.$top_ctn.html('<i class="icon icon-close close"></i>');
            s('.page-header-fixed .container').append(this.$top_ctn);
            this.$top_ctn.find('.close').on('click', function () {
                self.$top_ctn.addClass('hide');
            });
            return this.$top_ctn;
        }
    };

    return Message;
});
