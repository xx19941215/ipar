<div class="panel-header">
    <h3 class="title">{{panel_title}}</h3>
</div>
<div class="panel-body">
<form class="property-recommend-form panel-form hide">
    <input type="hidden" name="product_eid" value="">
    <input type="hidden" name="type" value="{{type}}">
    <input type="hidden" name="dst_eid" value="">

    <div class="title-wrap">
        <div class="title-box"><span class="title"></span><i class="icon icon-close close"></i></div>
    </div>

    <div class="content-wrap"><div class="content"></div></div>
    <div class="button-wrap">
        <a href="javascript:;" class="cancel">{{cancel}}</a>
        <input type="submit" class="button small" value="{{submit}}">
    </div>
</form>
<form class="property-rqt-form panel-form">
    <div><input type="text" name="title" required="required" value="{{title}}" placeholder="{{title_placeholder}}"></div>
    <div class="search-result hide"></div>
    <div class="zeditor-wrap">
        <div class="zeditor" data-name="content" data-placeholder="{{content_placeholder}}">{{content}}</div>
    </div>
    <div class="button-wrap">
        <a href="javascript:;" class="cancel">{{cancel}}</a>
        <input type="submit" class="button small" value="{{submit}}">
    </div>
    <input type="hidden" name="product_eid" value="{{product_eid}}">
    <input type="hidden" name="eid" value="{{eid}}">
    <input type="hidden" name="type" value="{{type}}">
</form>
</div>
