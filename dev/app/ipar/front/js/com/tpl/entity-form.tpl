<form class="entity-form">
    <div><input type="text" name="title" required="required" value="{{title}}" placeholder="{{title_placeholder}}"></div>
    <div class="zeditor-wrap">
        <div class="zeditor" data-name="content" data-placeholder="{{content_placeholder}}">{{content}}</div>
    </div>
    <div class="submit-wrap">
        <input type="submit" class="button small" value="{{submit}}">
    </div>
    <input type="hidden" name="eid" value="">
</form>
