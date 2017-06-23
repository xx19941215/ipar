<form class="product-form panel-form">
    <div>
        <input type="text" name="title" required="required" value="" placeholder="{{title_placeholder}}">
    </div>
    <div class="search-result hide"></div>
    <div class="zeditor-wrap">
        <div class="zeditor" data-name="content" data-placeholder="{{content_placeholder}}" data-required="required">{{content}}</div>
    </div>
    <div class="button-wrap">
        <a href="javascript:;" class="cancel">{{cancel}}</a>
        <input type="submit" class="button small" value="{{submit}}">
    </div>
    <input type="hidden" name="rqt_eid" value="{{rqt_eid}}">
    <input type="hidden" name="eid" value="{{eid}}">
</form>
