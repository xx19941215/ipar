define([
    "zjs/z",
    "zjs/z.selector",
    "zjs/z.net",
    "zjs/z.pop-panel",
    //"zjs/z.simple-template-engine",
], function(z, s, net, panel) {
    "use strict";

    var img = s(".bg"),
        wx_panel,
        $wx_qrcode = s(".show-qrcode");

    if ($wx_qrcode.length > 0) {
        wx_panel = panel();
        wx_panel.append(s(".qr-code"));
       
        $wx_qrcode.on("click", function () {
            wx_panel.show();
        });
    }

});
