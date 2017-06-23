<?php
$this
    ->set('service', [
        'auth' => 'Mars\Service\AuthService',
        'user' => 'Mars\Service\UserService',
        'user_wx' => 'Mars\Service\UserWxService',
        'user_wb' => 'Mars\Service\UserWbService',
        'role' => 'Mars\Service\RoleService',
        'entity' => 'Mars\Service\EntityService',
        'story' => 'Mars\Service\StoryService',

        'comment' => 'Mars\Service\CommentService',
        'entity_comment' => 'Mars\Service\EntityCommentService',
        'article_comment' => 'Mars\Service\ArticleCommentService',

        'tag' => 'Mars\Service\TagService',
        'entity_tag' => 'Mars\Service\EntityTagService',
        'tag_entity' => 'Mars\Service\TagEntityService',
        'follow' => 'Mars\Service\FollowService',
        'industry_tag' => 'Mars\Service\IndustryTagService',
        'brand_tag' => 'Mars\Service\BrandTagService',
        'company_brand_tag' => 'Mars\Service\CompanyBrandTagService',

        'user_follow' => 'Mars\Service\UserFollowService',
        'entity_follow' => 'Mars\Service\EntityFollowService',
        'group_follow' => 'Mars\Service\GroupFollowService',

        'entity_like' => 'Mars\Service\EntityLikeService',
        'solution_vote' => 'Mars\Service\SolutionVoteService',
        'property_vote' => 'Mars\Service\PropertyVoteService',


        'group' => 'Mars\Service\GroupService',
        'company' => 'Mars\Service\CompanyService',
        'company_industry_tag' => 'Mars\Service\CompanyIndustryTagService',

        'group_website' => 'Mars\Service\GroupWebsiteService',
        'group_social' => 'Mars\Service\GroupSocialService',
        'group_office' => 'Mars\Service\GroupOfficeService',
        'group_user' => 'Mars\Service\GroupUserService',
        'group_contact' => 'Mars\Service\GroupContactService',

        'address' => 'Mars\Service\AddressService',

        'js_trans' => 'Mars\Service\JsTransService',

        'product_purchase' => 'Mars\Service\ProductPurchaseService',
        'product_purchase_statistic' => 'Mars\Service\ProductPurchaseStatisticService',

        'company_product' => 'Mars\Service\CompanyProductService',
        'brand_tag_product' => 'Mars\Service\BrandTagProductService',
        'friend_link' => 'Mars\Service\FriendLinkService',
        'user_setting' => 'Mars\Service\UserSettingService',
        'user_email' => 'Mars\Service\UserEmailService',
        'user_nick' => 'Mars\Service\UserNickService',
        'user_password' => 'Mars\Service\UserPasswordService',
        'user_info' => 'Mars\Service\UserInfoService'
        /*
        'rqt' => 'Ipar\RqtService',
        'idea' => 'Ipar\IdeaService',
        'product' => 'Ipar\ProductService',
        'feature' => 'Ipar\FeatureService',

        'appearance' => 'Ipar\AppearanceService',
        'sketch' => 'Ipar\SketchService',

        'demo' => 'Ipar\DemoService',
        'pri' => 'Ipar\PriService',

        'team' => 'Ipar\TeamService',
        'meeting' => 'Ipar\MeetingService',
        'event' => '\Mars\Service\EventService',
        'invent' => 'Ipar\InventService',
         */
    ]);
