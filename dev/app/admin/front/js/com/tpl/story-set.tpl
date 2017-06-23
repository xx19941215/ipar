<div class="entity-set" id="entity-set-<%this.page%>">
<% for(var index in this.stories) { %>
<% var story=this.stories[index]; %>
    <div class="story">
        <div class="story-user-action">
            <div class="user">
                <a class="user-avt" href="javascript:;"><i class="icon icon-avt"></i></a>
            </div>
            <div class="main">
                <a class="user-nick"><%story.user_nick%></a>
                <div class="action">
                    <span class="text"><%story.action_text%></span>
                    <i class="icon icon-<%story.dst_type%>"></i>
                </div>
            </div>
            <div class="date"><%story.time_elapsed%></div>
        </div>

        <div class="story-entity">
            <% if (story.data_title) { %>
            <div class="data-title"><a href="javascript:;"><%story.data_title%></a></div>
            <% } %>
            <% if (story.data_abbr) {%>
            <div class="data-abbr">
                <%story.data_abbr%>
                <a href="javascript:;">:read-more</a>
            </div>
            <% } %>
        </div>

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
    </div>
<% } %>
</div>
