define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.pop-panel'
], function(z, s, net, panel) {
    'use strict';
    var $contact_add = s('.contact-add'),
        $contact_edit = s('.contact-edit'),
        $social = s('.social-edit'),
        panel = panel(),
        $contact_container = s('.contact-container'),
        $social_container = s('.social-container'),
        $office_container = s('.office-container'),
        gid = s('.gid')[0].value,
        company_title = s('.company-title').text(),
        $social_submit = s('.social-submit'),
        $contact_submit = s('.contact-submit');


    $contact_add.on('click', function() {

        s('input[class="contact_id"]')[0].value = "";
        s('input[class="linkman"]')[0].value = "";
        s('input[class="roles"]')[0].value = "";
        s('input[class="phone"]')[0].value = "";
        s('input[class="email"]')[0].value = "";
        panel.html("");
        panel.append($contact_container);
        panel.show();
    });

    $contact_submit.on('click', function() {
        var linkman = s('input[class="linkman"]')[0].value,
            roles = s('input[class="roles"]')[0].value,
            phone = s('input[class="phone"]')[0].value,
            email = s('input[class="email"]')[0].value,
            contact_id = s('input[class="contact_id"]')[0].value,
            fd = new window.FormData();

        fd.append('gid', gid);
        fd.append('name', linkman);
        fd.append('roles', roles);
        fd.append('phone', phone);
        fd.append('email', email);
        if (contact_id) {
            fd.append('contact_id', contact_id);
            ajax('update_contact', fd, ajaxOk);
            return;
        }
        ajax('create_contact', fd, ajaxOk);
    });

    $contact_edit.on('click', function() {

        s('input[class="linkman"]')[0].value = this.getAttribute('data-name');
        s('input[class="roles"]')[0].value = this.getAttribute('data-roles');
        s('input[class="phone"]')[0].value = this.getAttribute('data-phone');
        s('input[class="email"]')[0].value = this.getAttribute('data-email');
        s('input[class="contact_id"]')[0].value = this.getAttribute('data-id');
        panel.html("");
        panel.append($contact_container);
        panel.show();
    });

    var $contact_delete = s('.contact-delete');
    $contact_delete.on('click', function() {
        var contact_id = s('input[class="contact_id"]')[0].value,
            fd = new window.FormData();
            fd.append('contact_id', contact_id);
            fd.append('gid', gid);
            ajax('delete_contact', fd, ajaxOk);
    });
    var $office_add = s('.office-add'),
        $office_edit = s('.office-edit');
    $office_add.on('click', function() {
        s('.office_id')[0].value = "";
        s('input[class="address keyword"]')[0].value = "";
        s('input[class="address_id"]')[0].value = "";
        s('.office_address_id')[0].value = "";

        panel.html("");
        panel.append($office_container);
        panel.show();
    });

    $office_edit.on('click', function() {
        s('.office_id')[0].value = this.getAttribute('data-office-id');
        s('input[class="address keyword"]')[0].value = this.getAttribute('data-address');
        s('input[class="address_id"]')[0].value = this.getAttribute('data-area-id');
        s('.office_address_id')[0].value = this.getAttribute('data-address-id');

        panel.html("");
        panel.append($office_container);
        panel.show();
    });

    var $office_submit = s('.office-submit');
    $office_submit.on('click', function() {
        var office_id = s('.office_id')[0].value,
            address = s('input[class="address keyword"]')[0].value,
            area_id = s('input[class="address_id"]')[0].value,
            office_address_id = s('.office_address_id')[0].value,
            fd = new window.FormData();
            fd.append('gid', gid);
            fd.append('office_id', office_id);
            fd.append('address', address);
            fd.append('area_id', area_id);
            fd.append('office_address_id', office_address_id);
            if (office_id) {
                ajax('update_office', fd, ajaxOk);
            } else {
                ajax('create_office', fd, ajaxOk);
            }
    });

    var $office_delete = s('.office-delete');
    $office_delete.on('click', function() {
        var office_id = s('.office_id')[0].value,
            fd = new window.FormData();
            fd.append('office_id', office_id);
            fd.append('gid', gid);
            ajax('delete_office', fd, ajaxOk);
    });

    $social.on('click', function() {

        panel.html("");
        panel.append($social_container);
        panel.show();
    });

    $social_submit.on('click', function() {
        var website = s('input[class="website_url"]'),
            website_url = website[0].value,
            website_id = website.attr('data-id'),

            weibo = s('input[class="weibo_url"]'),
            weibo_url = weibo[0].value,
            weibo_id = weibo.attr('data-id'),

            wechat = s('input[class="wechat_url"]'),
            wechat_url = wechat[0].value,
            wechat_id = wechat.attr('data-id'),
            fd = new window.FormData();
        fd.append('gid', gid);
        fd.append('title', company_title);
        fd.append('website_url', website_url);
        fd.append('weibo_url', weibo_url);
        fd.append('wechat_url', wechat_url);
        fd.append('website_id', website_id);
        fd.append('weibo_id', weibo_id);
        fd.append('wechat_id', wechat_id);
        ajax('edit_social', fd, ajaxOk);
    });

    function ajaxOk(data) {
        var data = z.pack(data);
        if (data.isOk()) {
            window.location.reload();
        } else {
            var errors = s('.error');
            var string = "";
            for(var item in data.errors) {
                string += data.errors[item];
            }
            errors.html(string);
            errors.removeClass('hide');
        }
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

});
