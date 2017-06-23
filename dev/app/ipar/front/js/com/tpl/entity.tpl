<div class="entity eid-{{dst_eid}}" data-id="{{dst_eid}}" data-type={{dst_type_id}}>

    <div class="entity-title">
        <a target="_blank" href="{{url}}">{{title}}</a>
    </div>

    <!-- <div class="entity-abbr">{{abbr}}<a class="read-more" href="javascript:;">{{read_more}}</a></div> -->
    <div class="entity-content">{{content}}</div>

    <div class="entity-info">
    </div>
    <div class="story-ops">
        <span class="item">{{html_last_update}}</span>
        <a href="javascript:;" class="entity like-list">
            <i class="icon icon-like {{is_like}}"></i>
            <span class="text">{{like_text}}</span>
        </a>
        <a href="#" class="like-count">{{like_count}}</a>

        <span class="comment">
            <a href="javascript:;" class="show-comments">
                <i class="icon icon-comment"></i>
                <span class="text">{{comment_text}}</span>
            </a>
            <a href="#" class="comments-count">{{comment_count}}</a>
        </span>

        <div class="comments-container" data-comments-count={{comment_count}}>
            <div class="comments-list">

            </div>
            <div class="comment-action">
                <form class="comment-form">
                    <input type="hidden" name="dst_id" value="{{dst_eid}}">
                    <input type="hidden" name="dst_type_id" value="{{dst_type_id}}">

                    <textarea rows="1" type="text" name="content" required="required" class="comment-textbox
                        input-group-field"></textarea>
                    <input type="submit" class="submit-comment-button button" value="{{trans_comment}}">
                </form>
            </div>
        </div>

    </div>

</div>
<br/>
