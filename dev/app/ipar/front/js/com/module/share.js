define([
    "zjs/z",
    "zjs/z.selector",
], function(z, s) {
    "use strict";
    var $share_container = s('.social-share');
    if ($share_container.length == 0) return;

    require('scriptjs')('//cdn.bootcss.com/social-share.js/1.0.14/js/social-share.min.js', function () {


        function modify_link() {
            var image = s('.main p img')[0],
                weibo_share = s('.social-share .icon-weibo')[0];

            if (!weibo_share) {
                setTimeout(modify_link, 200);
                return;
            }

            if (!image) {
                weibo_share.href = weibo_share.href.replace('%25image%25', '').replace('&pic=', '');
                return;
            };
            weibo_share.href = weibo_share.href.replace('%25image%25', image.src);
        }

        modify_link();


    })
});
