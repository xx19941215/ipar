define([
    "zjs/z",
    "zjs/z.selector",
], function (z, s) {
    "use strict";

    function getScreenSize() {
        return {
            'height': $(window).height(),
            'width': $(window).width()
        }
    }

    function calcMargin() {
        var oSize = getScreenSize();
        var oLogo = $(".company-logo");
        var item = $(".company-logo .item");
        var itemMargin;

        if (oSize.width < 640) {
            itemMargin = (($(".company-logo").width() / 2 - 150) / 2) * 2;
            $(".company-logo .item").each(function () {
                $(this).css("margin-bottom", itemMargin - (item.height() - 150) + "px");
            });
        } else if (oSize.width < 992) {
            itemMargin = (($(".company-logo").width() / 3 - 150) / 2) * 2;
            $(".company-logo .item").each(function () {
                $(this).css("margin-bottom", itemMargin - (item.height() - 150) + "px");
            });
        } else {
            itemMargin = (($(".company-logo").width() / 3 - 200) / 2) * 2;
            $(".company-logo .item").each(function () {
                $(this).css("margin-bottom", itemMargin - (item.height() - 200) + "px");
            });
        }
    }

    function indicate() {
        $(".indicate a").click(function (e) {
            var src = e.target;
            var sectionClsName = $(src).attr("href").slice(1);

            var top = getTop(sectionClsName);
            slide(top);
        });
    }

    function slide(top) {
        $("html, body").animate({
            scrollTop: top + "px"
        }, {
            duration: 1000,
            easing: "swing"
        });
    }

    function numberJump(ele, num) {
        ele.animate({count: num}, {
            duration: 1500,
            step: function () {
                ele.text(String(parseInt(this.count)));
                if (parseInt(ele.text()) === parseInt(num)) {
                    $(this).stop();
                }
            }
        });
    }

    function isVisible($ele) {
        var scrollH = $(window).scrollTop(),
            winH = $(window).height(),
            top = $ele.offset().top;
        if (top < winH + scrollH) {
            return true;
        } else {
            return false;
        }
    }

    function jumpToForm() {
        $(".price-btn").each(function () {
            $(this).on("click",function () {
                slide(getTop("contact"));
            });
        })
    }

    function jumping() {
        var cat = $(".service-container");
        var links = cat.find("a");
        links.each(function (index, item) {
            $(this).on("mouseout", function () {
                $(this).find("b").eq(0).css("opacity", 0);
            });

            $(this).on("mouseover", function () {
                $(this).find("b").eq(0).addClass("fontFadeIn");
                $(this).find("b").eq(0).css("opacity", 1);
            });
        });

        links.each(function (index, item) {
            $(this).on("click", function () {
                switch (index) {
                    case 0:
                        slide(getTop("company_in"));
                        break;
                    case 1:
                        slide(getTop("marketing"));
                        break;
                    case 2:
                        slide(getTop("improve"));
                        break;
                    case 3:
                        slide(getTop("media"));
                        break;
                }
                $(this).find("b").eq(0).css("opacity", 0);
            })
        })
    }

    function getTop(sectionClsName) {
        return $("." + sectionClsName).offset().top;
    }

    function scrollListen() {
        $('.section').each(function (index) {
            var scrollH = $(window).scrollTop(),
                limit = $(window).height() / 2,
                top = $(this).offset().top,
                eleHeight = $(this).height();
            if (scrollH + limit < top || top + eleHeight - scrollH < limit) {
                $(".indicate").find("li").eq(index).removeClass("current");

            } else {
                $(".indicate").find("li").eq(index).addClass("current");

            }

        });
    }

    function indicateShowOrHide($li) {
        var $licurrent = $li.parent().find(".current");
        $licurrent.removeClass("current");
        $li.get(0).className = "current";
    }

    function initFirstHeight() {
        $(".problem").css("height", $(window).height() - 50 + "px");
    }

    function validate() {
        $("#info").validate({
            'rules': {
                'name': 'required',
                'phone': 'required',
                'email': 'required',
                'company': 'required',
                'job': 'required',
                'content': 'required'
            },
            'messages': {
                'name': z.trans('company-service-form-hint-1'),
                'phone': z.trans('company-service-form-hint-2'),
                'email': z.trans('company-service-form-hint-3'),
                'company': z.trans('company-service-form-hint-4'),
                'job': z.trans('company-service-form-hint-5'),
                'content': z.trans('company-service-form-hint-6')
            },
            submitHandler: function (form) {
                $.get('/zh-cn/company-submit', {
                    name: $("#name").val(),
                    phone: $("#phone").val(),
                    email: $('#email').val(),
                    company: $('#company').val()
                    ,
                    job: $('#job').val(),
                    content: $('#content').val()
                }, function (text, status) {

                    $(".msg-ok").show();
                    $(".msg-ok").click(function () {
                        $(this).hide();
                    });

                    $("#info").find("[required]").each(function () {
                        $(this).val("");
                    });
                });

            }
        });
    }

    function page_company_service () {
            initFirstHeight();
            calcMargin();
            indicate();
            jumping();
            validate();
            jumpToForm();
            //数字跳动
            var numbers = $(".service-number");
            $(window).on('scroll load', function () {
                if (isVisible(numbers)) {
                    numberJump(numbers.find("li").eq(0).find("span").eq(0), $("[data-nums]").eq(0).data('nums'));
                    numberJump(numbers.find("li").eq(1).find("span").eq(0), $("[data-nums]").eq(1).data('nums'));
                    numberJump(numbers.find("li").eq(2).find("span").eq(0), $("[data-nums]").eq(2).data('nums'));
                }
                scrollListen();
            });
    }

    if(s('.ipar-ui-company_service-show').length > 0){
        require.ensure([], function () {
            require('scriptjs')('//cdn.bootcss.com/jquery/2.2.4/jquery.min.js', function () {
                require('scriptjs')('http://static.runoob.com/assets/jquery-validation-1.14.0/dist/jquery.validate.min.js', page_company_service);
            })
        });
    }
});
