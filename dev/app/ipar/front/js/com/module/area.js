define([
    "zjs/z",
    "zjs/z.selector",
    "zjs/z.net",
    "zjs/z.pop-panel",
    //"zjs/z.simple-template-engine",
], function(z, s, net, panel) {
    "use strict";
    var city_code,
        host_url = "http://map.baidu.com/su?callback=ipar&type=0",
        url,
        ajax_loading = false,
        address_keyword,
        suggestion_html = "",
        area_id = 0,
        data_area_id = 0;


    var  province;
    var area_ids = {'中国':1,'北京':2,'天津':19,'河北':36,'山西':48,'蒙古':60,'辽宁':73,'吉林':88,'龙江':98,'上海':112,'江苏':131,'浙江':145,'安徽':157,'福建':174,'江西':184,'山东':196,'河南':214,'湖北':233,'湖南':251,'广东':266,'广西':288,'海南':303,'重庆':323,'四川':365,'贵州':387,'云南':397,'西藏':414,'陕西':422,'甘肃':433,'青海':448,'宁夏':457,'新疆':463,'台湾':483,'香港':507,'澳门':526};

    var $keyword = s('.keyword');
    var $suggestion = s('#suggestion');

    function ajax(url) {
        require("scriptjs")(url, function(){
            ajax_loading = false;
        });
    }


    window.iplocation = function (data) {
        city_code = data.content.address_detail.city_code;
        url = host_url+"&cid="+city_code;
        if (!$keyword[0].value) {
            $keyword[0].value = data.content.address;
        }
    }

    window.ipar = function(data) {

        var content = data.s;
        suggestion_html = "";

        for (var item in content) {
            stringParser(content[item]);
        }
        if (content.length > 3) {
            $suggestion.html(suggestion_html);
        } else {
            $suggestion.html("");
        }
    }

    window.select = function(args) {
        var address;
        console.log(args);
        province = address_keyword.substring(0,address_keyword.indexOf('省')+1);

        if (province) {

            var length = province.length;
            area_id = area_ids[province.substring(length-3,length-1)];
        } else {
            province = address_keyword.substring(0,address_keyword.indexOf('市')+1);
            var length = province.length;
            area_id = area_ids[province.substring(length-3,length-1)];
            province = '';
        }
        if (args.indexOf('省') > 0) {
            address = "";
        } else {
            address =  province;
        }
        var data = args.split(",");
        console.log(data);
        for(var item in data) {
            address += data[item];
        }

        $keyword[0].value = address;
        if (address_keyword != $keyword[0].value) {
            address_keyword = $keyword[0].value;
            var tmp = url+"&wd="+address_keyword;
            ajax(tmp);
            ajax_loading = true;
        }
        if (area_id !=0) {
                subAreaId(data.slice(0,3).join(","));
        }
    }
    function subAreaId(title) {

        var fd = new window.FormData();
        fd.append('parent_id', area_id);
        fd.append('title', title);
        fd.append("_token", config.token);
        net.ajax({
            dataType: 'json',
            url: z.config('api').area_sch,
            method: 'post',
            withCredentials: true
        }).done(function(datas) {
            var data = z.pack(datas);
            if (data.isOk()) {
                data_area_id = data.getItem('areas');
                s('.address_id')[0].value = data_area_id[0].id;
                area_id = data_area_id[0].id;
            }
        }).send(fd);
    }
    function stringParser(e) {

        var address = e.substring(0,e.substring(0, (e.length-2)).lastIndexOf("$")); 
        var $$$ = address.indexOf("$$$");
        if ( $$$ >= 0) {

            var state = address.substring($$$+3);
            var str = [address.substring(0,$$$),state];
            suggestion_html += '<div class="auto" onclick="select(\''+str+'\');">'+str.join("").replace(/\$/g, "")+'</div>';
            return;
        }
        var f$ = address.indexOf("$");
        var f$$ = address.indexOf("$$");
        address = address.replace(/\$/, '');
        var city = address.substring(0,f$);
        var state = address.substring(f$,f$$-1);
        var street = address.substring(f$$+1);

        var str = [province, city, state, street];

            suggestion_html += '<div class="auto" onclick="select(\''+str+'\');">'+str.join("")+'</div>';
    }

    var $input = s('input[type="text"]');
    $input.on('focus', function(){
        $suggestion.html("");
    });
    $keyword.on('keyup', function(e){
        if (ajax_loading == false ) { 
            if (address_keyword != this.value) {
                address_keyword = this.value;
                var tmp = url+"&wd="+address_keyword;
                ajax(tmp);
                ajax_loading = true;
            }
        }
    });

    z.ready(function() {
        console.log(area_ids);
        var url = "http://api.map.baidu.com/location/ip?&ak=OAMgSoz7uMB0VldQAwpKORqu127YxctO&callback=iplocation";
        ajax(url);
    });
});
