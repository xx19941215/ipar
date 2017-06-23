<form class="rqt-form panel-form">
    <div><input type="text" name="title" required="required" value="" placeholder="{{title_placeholder}}"></div>
    <div class="search-result hide"></div>
    <div class="zeditor-wrap">
        <div class="zeditor" data-name="content" data-placeholder="{{content_placeholder}}">{{content}}</div>
    </div>
    <div class="button-wrap">
        <a href="javascript:;" class="cancel">{{cancel}}</a>
        <input type="submit" class="button small" value="{{submit}}">
    </div>
    <input type="hidden" name="eid" value="{{eid}}">
    <input type="hidden" name="product_eid" value="{{product_eid}}">
    <input type="hidden" name="type_key" value="{{type_key}}">
</form>
