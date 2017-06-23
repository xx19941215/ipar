<?php
$this
    ->set('repo', [
        'auth' => 'Mars\Repo\AuthRepo',
        'user' => 'Mars\Repo\UserRepo',
        'user_wx' => 'Mars\Repo\UserWxRepo',
        'user_wb' => 'Mars\Repo\UserWbRepo',
        'role' => 'Mars\Repo\RoleRepo',
        'entity' => 'Mars\Repo\EntityRepo',
        'story' => 'Mars\Repo\StoryRepo',

        'comment' => 'Mars\Repo\CommentRepo',
        'entity_comment' => 'Mars\Repo\EntityCommentTableRepo',
        'article_comment' => 'Mars\Repo\ArticleCommentTableRepo',

        'follow' => 'Mars\Repo\FollowRepo',
        'tag' => 'Mars\Repo\TagTableRepo',
        'entity_tag' => 'Mars\Repo\EntityTagRepo',
        'entity_tag_table' => 'Mars\Repo\EntityTagTableRepo',
        'tag_entity' => 'Mars\Repo\TagEntityRepo',
        'industry_tag' => 'Mars\Repo\IndustryTagRepo',
        'brand_tag' => 'Mars\Repo\BrandTagRepo',
        'company_brand_tag' => 'Mars\Repo\CompanyBrandTagRepo',

        'user_follow' => 'Mars\Repo\UserFollowRepo',
        'entity_follow' => 'Mars\Repo\EntityFollowRepo',
        'group_follow' => 'Mars\Repo\GroupFollowRepo',

        'entity_like' => 'Mars\Repo\EntityLikeRepo',
        'solution_vote' => 'Mars\Repo\SolutionVoteRepo',
        'property_vote' => 'Mars\Repo\PropertyVoteRepo',


        'group' => 'Mars\Repo\GroupTableRepo',
        'company' => 'Mars\Repo\CompanyRepo',
        'company_industry_tag' => 'Mars\Repo\CompanyIndustryTagTableRepo',

        'group_website' => 'Mars\Repo\GroupWebsiteRepo',
        'group_social' => 'Mars\Repo\GroupSocialRepo',
        'group_office' => 'Mars\Repo\GroupOfficeRepo',
        'group_user' => 'Mars\Repo\GroupUserRepo',
        'group_contact' => 'Mars\Repo\GroupContactTableRepo',

        'address' => 'Mars\Repo\AddressRepo',

        'js_trans' => 'Mars\Repo\JsTransRepo',


        'product_purchase' =>'Mars\Repo\ProductPurchaseRepo',
        'product_purchase_statistic' => 'Mars\Repo\ProductPurchaseStatisticRepo',

        'company_product' => 'Mars\Repo\CompanyProductRepo',
        'brand_tag_product' => 'Mars\Repo\BrandTagProductTableRepo',
        'friend_link' => 'Mars\Repo\FriendLinkRepo',
        'user_setting' => 'Mars\Repo\UserSettingRepo',
        'user_email' => 'Mars\Repo\UserEmailRepo',
        'user_nick' => 'Mars\Repo\UserNickRepo',
        'user_password' => 'Mars\Repo\UserPasswordRepo',
        'user_info' => 'Mars\Repo\UserInfoRepo'

        /*
        'rqt' => 'Ipar\RqtRepo',
        'idea' => 'Ipar\IdeaRepo',
        'feature' => 'Ipar\FeatureRepo',
        'product' => 'Ipar\ProductRepo',


        'rqt_feature' => 'Ipar\RqtFeatureRepo',
        'event' => 'EventRepo',

        'invent' => 'Ipar\InventRepo',
        'invent_rqt' => 'Ipar\InventRqtRepo',
        'invent_feature' => 'Ipar\InventFeatureRepo',
        'invent_team' => 'Ipar\InventTeamRepo',
        'invent_meeting' => 'Ipar\InventMeetingRepo',


        'appearance' => 'Ipar\AppearanceRepo',
        'sketch' => 'Ipar\SketchRepo',

        'team' => 'Ipar\TeamRepo',
        'team_member' => 'Ipar\TeamMemberRepo',

        'meeting' => 'Ipar\MeetingRepo',
        'meeting_member' => 'Ipar\MeetingMemberRepo',

        'demo' => 'Ipar\DemoRepo',

        'pri' => 'Ipar\PriRepo', //released product item
        'pri_rqt' => 'Ipar\PriRqtRepo',
        'pri_feature' => 'Ipar\PriFeatureRepo'
         */
    ]);
