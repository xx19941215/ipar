define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'module/controller-util',
    'module/auth'
], function (z, s, net, controller_util, auth) {
    'use strict';

    s('.add-property-feature').on('click', function () {
        if (!auth.requireLogin()) {
            return;
        }
        controller_util.get_feature_controller()
            .showPanel({
                panel_title: z.trans('add-property-feature'),
                submit_route: 'product_save_feature',
                recommend_route: 'product_save_property',
                product_eid: s(this).attr('data-product_eid')
            });
    });

    s('.add-property-solved').on('click', function () {
        if (!auth.requireLogin()) {
            return;
        }
        controller_util.get_rqt_controller()
            .showPanel({
                panel_title: z.trans('add-property-solved'),
                submit_route: 'product_save_solved',
                recommend_route: 'product_save_property',
                product_eid: s(this).attr('data-product_eid'),
                type_key: 'solved'
            });
    });

    s('.add-property-improving').on('click', function () {
        if (!auth.requireLogin()) {
            return;
        }
        controller_util.get_rqt_controller()
            .showPanel({
                panel_title: z.trans('add-property-improving'),
                submit_route: 'product_save_improving',
                recommend_route: 'product_save_property',
                product_eid: s(this).attr('data-product_eid'),
                type_key: 'improving'
            });
    });
});
