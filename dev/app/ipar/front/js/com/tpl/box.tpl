<div class="box-wrap">
    <div class="box entity">
        <div class="box-cover icon">
            <a target="_blank" class="link" href="{{url}}">{{html_cover}}</a>
        </div>

        <h3 class="box-title">
            <a target="_blank" href="{{url}}">{{title}}</a>
        </h3>

        <div class="box-ops clearfix eid-{{dst_eid}}" data-id="{{dst_eid}}">
            <span class="item vote">
                <a href="javascript:;" class="like-list">
                    <i class="icon icon-like {{is_like}}"></i>
                    <span class="text">{{like_text}}</span>
                </a>
                <a href="javascript:;" class="like-count">{{like_count}}</a>
            </span>
            <span class="item">
                <a id="icon-edit-new" href="{{url}}">
                    <i class="icon icon-edit-new"></i>
                    <span class="text">{{trans_improving}}</span>
                </a>
                <a href="javascript:;"> {{improving_count}}</a>
            </span>
        </div>

    </div>
</div>