<div class="property entity property-box" data-id="{{dst_eid}}" data-type="{{dst_type_id}}">

    <div class="entity-title">
        <a href="{{url}}" target="_blank">{{title}}</a>
        <i class="icon icon-{{type}}"></i>
    </div>

    <div class="entity-abbr">{{abbr}}</div>
    <div class="entity-content">{{content}}</div>
    {{html_imgs}}

    <div class="entity-info">
        <span class="item">{{html_last_update}}</span>
        <span class="item property-id-{{id}}" data-pid="{{id}}">
            <a class="property-vote" href="javascript:;">
                <i class="icon icon-vote {{is_vote}}"></i>
                <span class="text">{{vote_text}}</span>
            </a>
            <a class="property-vote-count" href="javascript:;">
                {{vote_count}}
            </a>
        </span>

            <span class="comment">
                <a href="javascript:;" class="show-comments">
                    <i class="icon icon-comment"></i>
                    <span class="text">{{comment_text}}</span>
                </a>
                <a href="#">{{comment_count}}</a>
            </span>

            <div class="comments-container" data-comments-count={{entity_comments_count}}>
                <div class="comments-list">
                    <!-- comments -->
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
