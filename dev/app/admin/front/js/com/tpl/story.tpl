<div class="story">
    {{html_story_src}}
    <div class="story-user-action">
        <div class="user">
            <a class="user-avt" href="javascript:;"><i class="icon icon-avt"></i></a>
        </div>
        <div class="main">
            <a class="user-nick">{{user_nick}}</a>
            <div class="action">
                <span class="text">{{action_text}}</span>
                <i class="icon icon-{{dst_type}}"></i>
            </div>
        </div>
        <div class="date">{{time_elapsed}}</div>
    </div>

    <div class="entity">
        <div class="entity-title">
            <a href="{{entity_url}}">{{entity_title}}</a>
            <i class="icon icon-{{entity_type}}"></i>
        </div>

        <div class="entity-abbr">{{entity_abbr}}<a class="read-more" href="javascript:;">{{read_more}}</a></div>
        {{html_entity_imgs}}
        <div class="entity-content">{{entity_content}}</div>

    </div>


    <!--
    <div class="story-ops">
        <span class="vote">
            <a href="#"><i class="icon icon-vote"></i></a>
            <a href="#">点赞</a>
            <a href="#">15</a>
        </span>
        <span class="comment">
            <a href="#"><i class="icon icon-comment"></i></a>
            <a href="#">评论</a>
            <a href="#">35</a>
        </span>
    </div>
    -->
</div>
