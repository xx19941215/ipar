define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'module/controller-util',
    'module/auth'
], function (z, s, net, controller_util, auth) {
    'use strict';

    s('.add-solution-idea').on('click', function () {

        if(!auth.requireLogin())
        {
            return false;
        }
        controller_util.get_idea_controller()
            .showPanel({
                panel_title: z.trans('add-solution-idea'),
                submit_route: 'rqt_save_idea',
                rqt_eid: s(this).attr('data-rqt_eid')
            });
    });

    s('.add-solution-product').on('click', function () {
        if(!auth.requireLogin())
        {
            return false;
        }
        controller_util.get_product_controller()
            .showPanel({
                panel_title: z.trans('add-solution-product'),
                submit_route: 'rqt_save_product',
                recommend_route: 'rqt_save_solution',
                rqt_eid: s(this).attr('data-rqt_eid')
            });
    });
});
