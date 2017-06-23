define([
    'zjs/z',
    'zjs/z.selector',
    'module/rqt-controller',
    'module/product-controller',
    'module/idea-controller',
    'module/feature-controller'
], function(z, s, RqtController, ProductController, IdeaController, FeatureController) {
    'use strict';

    var rqt_controller,
        product_controller,
        idea_controller,
        feature_controller;

    var controller_util = {
        get_rqt_controller: function () {
            if (rqt_controller) {
                return rqt_controller;
            }
            rqt_controller = new RqtController();
            return rqt_controller;
        },
        get_product_controller: function () {
            if (product_controller) {
                return product_controller;
            }

            product_controller = new ProductController();
            return product_controller;
        },
        get_idea_controller: function () {
            if (idea_controller) {
                return idea_controller;
            }

            idea_controller = new IdeaController();
            return idea_controller;
        },
        get_feature_controller: function () {
            if (feature_controller) {
                return feature_controller;
            }

            feature_controller = new FeatureController();
            return feature_controller;
        }
    };

    return controller_util;
});
