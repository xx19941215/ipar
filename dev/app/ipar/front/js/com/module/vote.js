define([
    'zjs/z',
    'zjs/z.selector',
    'zjs/z.net',
    'zjs/z.pop-panel',
    'zjs/z.simple-template-engine',
    'module/auth'
], function(z, s, net, panel, tpl,auth) {
    'use strict';

    var init = function() {
        s('.item .property-vote-bak').on('click', function() {
            var solution_id = this.parentElement.getAttribute('data-pid'),
                route = 'vote_solution',
                $icon = s(this.childNodes.item(1)),
                $vote_count = s(this.nextElementSibling),
                voted = $icon.hasClass('voted');

            if (voted) {
                route = 'unvote_solution';
            }
            net.routePost(route, {
                'solution_id': solution_id
            }, function(pack) {
                if (pack.isOk()) {
                    if (voted) {
                        $icon.removeClass('voted');
                        $vote_count.html(parseInt($vote_count.html()) - 1);
                    } else {
                        $icon.addClass('voted');
                        $vote_count.html(parseInt($vote_count.html()) + 1);
                    }
                }
            });
        });

        s('.vote-count-bak').on('click', function() {
            if(!auth.requireLogin()){
                return;
            }
            var solution_id = this.parentElement.getAttribute('data-sid');
            if (parseInt(this.textContent) == 0) {
                return;
            }
            net.routePost('solution_users', {
                'solution_id': solution_id
            }, function(pack) {
                if (pack.isOk()) {
                    s('.user-body').html("");
                    s('.user-container-title').html(z.trans("vote-user"));
                    showPanel(pack);
                    $user_container.attr('data-url', 'solution_users');
                    user_panel.show();
                }
            });
        });

        s('.property-vote').on('click', function() {
            if(!auth.requireLogin()){
                return;
            }

            var property_id = this.parentElement.getAttribute('data-pid'),
                route = 'vote_property',
                $icon = s(this.childNodes.item(1)),
                $vote_count = s(this.nextElementSibling),
                voted = $icon.hasClass('voted');

            if (voted) {
                route = 'unvote_property';
            }
            net.routePost(route, {
                'property_id': property_id
            }, function(pack) {
                if (pack.isOk()) {
                    if (voted) {
                        $icon.removeClass('voted');
                        $vote_count.html(parseInt($vote_count.html()) - 1);
                    } else {
                        $icon.addClass('voted');
                        $vote_count.html(parseInt($vote_count.html()) + 1);
                    }
                }
            });
        });

        s('.solution-vote').on('click', function() {
            if (!auth.requireLogin()) {
               return;
            }
            var solution_id = this.parentElement.getAttribute('data-pid'),
                route = 'vote_solution',
                $icon = s(this.childNodes.item(1)),
                $vote_count = s(this.nextElementSibling),
                voted = $icon.hasClass('voted');

            if (voted) {
                route = 'unvote_solution';
            }
            net.routePost(route, {
                'solution_id': solution_id
            }, function(pack) {
                if (pack.isOk()) {
                    if (voted) {
                        $icon.removeClass('voted');
                        $vote_count.html(parseInt($vote_count.html()) - 1);
                    } else {
                        $icon.addClass('voted');
                        $vote_count.html(parseInt($vote_count.html()) + 1);
                    }
                }
            });
        });

        s('.property-vote-count').on('click', function() {
            if (!auth.requireLogin()) {
               return;
            }
            var property_id = this.parentElement.getAttribute('data-pid');
            if (parseInt(this.textContent) == 0) {
                return;
            }
            net.routePost('property_users', {
                'property_id': property_id
            }, function(pack) {
                if (pack.isOk()) {
                    s('.user-body').html("");
                    s('.user-container-title').html(z.trans("vote-user"));
                    showPanel(pack);
                    $user_container.attr('data-url', 'property_users');
                    user_panel.show();
                }
            });
        });

        s('.solution-vote-count').on('click', function() {
            if (!auth.requireLogin()) {
               return;
            }
            var solution_id = this.parentElement.getAttribute('data-pid');
            if (parseInt(this.textContent) == 0) {
                return;
            }
            net.routePost('solution_users', {
                'solution_id': solution_id
            }, function(pack) {
                if (pack.isOk()) {
                    s('.user-body').html("");
                    s('.user-container-title').html(z.trans("vote-user"));
                    showPanel(pack);
                    $user_container.attr('data-url', 'solution_users');
                    user_panel.show();
                }
            });
        });



    }



    return init;

});
