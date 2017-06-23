<div class="story">
    {{html_story_src}}

    <div class="entity eid-{{dst_eid}}" data-id="{{dst_eid}}" data-type={{dst_type_id}}>
        <div class="entity-title">
            <a href="{{entity_url}}" target="_blank">{{entity_title}}</a>
            <!-- <i class="icon icon-{{entity_type}}"></i> -->
        </div>
        <div class="date">{{time_elapsed}}</div>


        <div class="story-ops">
            <a href="javascript:;" class="entity like-list">
                <i class="icon icon-like {{is_like}}"></i>
                <span class="text">{{like_text}}</span>
            </a>
            <a href="javascript:;" class="like-count">{{like_count}}</a>
            <span class="comment">
                <a href="javascript:;" class="show-comments">
                    <i class="icon icon-comment"></i>
                    <span class="text">{{comment_text}}</span>
                </a>
                <a href="javascript:;" class="comments-count">{{comment_count}}</a>
            </span>
            <span class="count_product">
                <i class="icon icon-cube"></i>
                <a target="_blank" href="{{route_product}}">
                    {{trans_product}} {{count_product}}
                </a>
            </span>
            <span class="count_idea">
                <i class="icon icon-idea"></i>
                <a target="_blank" href="{{route_idea}}">
                    {{trans_idea}} {{count_idea}}
                </a>
            </span>

            <div class="comments-container" data-comments-count={{comment_count}}>
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

</div>
