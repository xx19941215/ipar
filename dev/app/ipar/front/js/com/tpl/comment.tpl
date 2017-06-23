<div class="comment" data-id="{{id}}" data-uid="{{uid}}" data-reply-user-nick="{{reply_user_nick}}">
    <div class="user">
        {{user_avt}}
    </div>
    <div class="main">

        <p class="user-info">
            <span class="user-nick">{{user_nick}}</span>
            <span class="reply-text" style="display:none">replied to</span>
            <span class="reply-user-nick"></span>
        </p>

        <p>{{content}}</p>
    </div>
    <div class="date">{{created}}</div>
    <div class="action">
        <a href="javascript:;" class="delete-comment-button entity-unbind" style="display:none">{{trans_delete}}</a>
        <a href="javascript:;" class="reply-comment-button">{{trans_reply}}</a>
    </div>
</div>
