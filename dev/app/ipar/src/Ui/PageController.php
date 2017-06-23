<?php
namespace Ipar\Ui;

class PageController extends IparControllerBase
{
    public function aboutUs()
    {
        return $this->page('page/about-us');
    }

    public function joinUs()
    {
        return $this->page('page/join-us');
    }

    public function bike()
    {
        return $this->page('page/bike');
    }

    public function links()
    {
        $friend_link_set = $this->service('friend_link')->findLinkSet();
        $iconSet = ['icon-website-industy',
            'icon-website-shop',
            'icon-website-mp',
            'icon-website-crowdsourcing',
            'icon-website-customized',
            'icon-website-entertainment',
            'icon-website-design',
            'icon-website-zone',
            'icon-website-crowdfunding'
        ];
        return $this->page('page/links', [
            'friend_link_set' => $friend_link_set,
            'icon_set' => $iconSet
        ]);
    }

    public function foundation()
    {
        $follow_set = service('user_follow')->fetchPopularUsers();
        return $this->page('page/foundation', [
            'rank' => $follow_set
        ]);
    }

    public function umbrella()
    {
        $hotList = $this->service('activity')->getPopImproving();
        $hotList = $hotList->setCountPerPage(0)->getItems();
        $LikeItems = $this->service('activity')->getIsLike()->getItems();
        $LikeNum = $this->service('activity')->getLikeCount();
        $productEid = [2511, 2512, 2513, 2514, 2515, 1344, 2531, 2532, 2533, 2541, 2542, 2544];
        foreach ($productEid as $eid) {
            $improveNum[$eid] = $this->service('product')->countPimproving($eid);
        }

        foreach ($LikeItems as &$r) {
            $r = $r->eid;
        }
        $items = [];
        foreach ($hotList as $key => &$r) {
            $user = user_service()->getUserByUid($r->uid);
            $items[$key]['user'] = $user->nick;
            $items[$key]['user_url'] = $user->getUrl();
            if ($avt = $user->getAvt()) {
                $items[$key]['user_avt'] = '<img src="' . img_src($avt, 'small') . '">';
            } else {
                $items[$key]['user_avt'] = '<i class="icon icon-avt"></i>';
            }
            $items[$key]['created'] = $r->created;
            $items[$key]['content'] = $r->content;
            $items[$key]['title'] = $r->title;
            $items[$key]['product_title'] = $r->src_title;
            $items[$key]['like_count'] = $r->countLike($r->dst_eid);
            $items[$key]['comment_count'] = $r->countComment($r->dst_eid);
            $items[$key]['url'] = route_url('ipar-rqt-show', ['zcode' => $r->getZcodeByEid($r->dst_eid)]);
            $items[$key]['purl'] = route_url('ipar-product-show', ['zcode' => $r->src_zcode]);
        }
        return $this->page('page/umbrella', [
            'hotList' => array_slice($items, 0, 5),
            'improveNum' => $improveNum,
            'LikeNum' => $LikeNum,
            'LikeItems' => $LikeItems
        ]);
    }
}
