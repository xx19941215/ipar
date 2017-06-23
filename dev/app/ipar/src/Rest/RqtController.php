<?php
namespace Ipar\Rest;

class RqtController extends \Gap\Routing\Controller {

    public function index() {
        $item = [];

        $query = [
            'type_key' => $this->request->query->get('type_key'),
            'tag_id' => $this->request->query->get('tag_id'),
            'eid' => $this->request->query->get('eid'),
            'uid' => $this->request->query->get('uid'),
            'sort' => $this->request->query->get('sort')
        ];

        $rqts = service('rqt')
                ->getRqtSet($query)
                ->setCurrentPage($this->request->query->get('page'))
                ->getItems();

        foreach ($rqts as $rqt) {
            $item['eid'] = $rqt->eid;
            $item['dst_eid'] = $rqt->eid;
            $item['dst_type_id'] = 1;
            $item['zcode'] = $rqt->zcode;
            $item['imgs'] = $rqt->imgs;
            $item['changed'] = $rqt->changed;
            $item['entity_title'] = $rqt->title;
            $item['content'] = $rqt->content;
            $dst_url = route_url("ipar-rqt-show", ['zcode' => $rqt->zcode]);
            $item['entity_url'] = $dst_url;

            $user = user($rqt->uid);

            if (!$user) {
                continue;
            }

            $item['user_nick'] = $user->nick;
            $item['user_url'] = $user->getUrl();
            if ($avt = $user->getAvt()) {
                $item['user_avt'] = '<img src="' . img_src($avt, 'small') . '">';
            } else {
                $item['user_avt'] = '<i class="icon icon-avt"></i>';
            }

            if ($imgs = $rqt->getImgs()) {
                $img_count = count($imgs);
                $img_count = $img_count > 2 ? 2 : $img_count;
                $img_arr = [];
                $img_arr[] = '<div class="entity-imgs">';
                for ($i = 0; $i < $img_count; $i++) {
                    $img = $imgs[$i];
                    $img_arr[] = '<img class="img_limit" src="' . img_src($img, 'cover') . '">';
                }
                $img_arr[] = '</div>';
                $item['html_entity_imgs'] = implode($img_arr);
            }

            $abbr = $rqt->getDataAbbr();
            if (isset($abbr[97])) {
                $abbr .= '...<a class="read-more" href="javascript:;">' . trans('read-more') . '</a>';
            }

            $item['entity_abbr'] = $abbr;

            $item['comment_text'] = trans('comment');
            $item['comment_count'] = $rqt->countComment();
            $item['is_like'] = $this->service('entity_like')->isLike(['eid'=> $rqt->eid]) ? 'liked' : '';
            $item['like_text'] = trans('like');
            $item['like_count'] = $this->service('entity_like')->schEntityLikeSet(['eid' => $rqt->eid])->getItemCount();

            $item['time_elapsed'] = time_elapsed_string($rqt->created);

            $item['count_product'] = $this->service('rqt')->countSproduct($rqt->eid);
            $item['count_idea'] = $this->service('rqt')->countSidea($rqt->eid);
            $item['route_idea'] = route_url('ipar-rqt-idea', ['zcode' => $rqt->zcode]);
            $item['route_product'] = route_url('ipar-rqt-product', ['zcode' => $rqt->zcode]);

            $item['trans_product'] = trans('associate-product');
            $item['trans_idea'] = trans('associate-idea');

            $arr[] = $item;

        }

        return $this->packItem('story', $arr);
    }

    public function solution()
    {
        $eid = $this->request->query->get('eid');
        if (!$eid) {
            return [];
        }

        $stype_key = $this->request->query->get('stype_key');
        if (!$stype_key) {
            return;
        }

        $properties = $this->service('rqt')
            ->schSolutionSet($eid, [
                'stype_key' => $stype_key
            ])
            ->setCurrentPage($this->request->query->get('page'))
            ->getItems();

        $arr = [];

        foreach ($properties as $solution) {
            $type_key = $solution->getTypeKey();
            if (!$title = $solution->getTitle()) {
                $title = "$type_key#{$solution->zcode}";
            }
            $url = route_url("ipar-$type_key-show", ['zcode' => $solution->zcode]);
            $item = [
                'id' => $solution->id,
                'url' => $url,
                'stype_key' => get_type_key($solution->stype_id),
                'type_key' => $type_key,
                'zcode' => $solution->zcode,
                'title' => $title,
                //'abbr' => $solution->getAbbr(),
                'content' => $solution->getContent(),
                //'read_more' => trans('read-more'),
                'html_last_update' => implode([
                    '<a href="javascript:;">',
                    $solution->uid ? user($solution->uid)->nick : '',
                    '</a>',
                    trans('last-update'),
                    ': ',
                    time_elapsed_string($solution->changed)
                ]),
                'html_submits' => trans('%s-submits', $solution->countSubmit()),
                'dst_eid' => $solution->eid,
                'dst_type_id' => $solution->type_id,
                'entity_comments_count' => $solution->countComment()
            ];

            $item['is_voted'] = $this->service('solution')->isVoted($solution->id,current_uid()) ? 'voted' : '';

            if ($imgs = $solution->getImgs()) {
                $img_count = count($imgs);
                $img_count = $img_count > 2 ? 2 : $img_count;
                $img_arr = [];
                $img_arr[] = '<div class="entity-imgs">';
                for ($i = 0; $i < $img_count; $i++) {
                    $img = $imgs[$i];
                    $img_arr[] = '<img src="' . img_src($img, 'cover') . '">';
                }
                $img_arr[] = '</div>';
                $item['html_imgs'] = implode($img_arr);
            }
            $abbr = $solution->getAbbr();
            if (isset($abbr[97])) {
                $abbr .= '<a class="read-more" href="javascript:;">' . trans('read-more') . '</a>';
            } else {
                if (!$imgs) {
                    $abbr = $item['content'];
                }
            }

            $item['abbr'] = $abbr;

            $item['comment_text'] = trans('comment');
            $item['comment_count'] = $solution->countComment();

            $item['vote_text'] = trans('vote');
            $item['vote_count'] = $this->service('solution_vote')->countVoteUser($solution->id);

            $arr[] = $item;
        }
        return $this->packItem('solution', $arr);
    }

    public function savePost()
    {
        $rqt_service = service('rqt');

        $eid = (int) $this->request->request->get('eid');
        $title = $this->request->request->get('title');
        $content = $this->request->request->get('content');

        if ($eid > 0) {
            $pack = $rqt_service->updateRqt($eid, $title, $content);
            if ($pack->isOk()) {
                $pack->addItem('eid', $eid);
                $pack->addItem('title', $title);
                $pack->addItem('content', $content);
            }
            return $pack;
        }

        $pack = $rqt_service->createRqt($title, $content);
        if ($pack->isOk()) {
            $rqt = $rqt_service->getEntityByEid($pack->getItem('eid'));
            $link = '<a href="' . route_url('ipar-rqt-show', ['zcode' => $rqt->zcode]) . '">' . $rqt->title . '</a>';
            $pack->addItem('html_message', trans('created-rqt') . ', ' . $link);
        }
        return $pack;
    }

    public function saveIdeaPost()
    {
        $pack = service('rqt')->createSidea(
            $this->request->request->get('rqt_eid'),
            $this->request->request->get('content')
        );
        if ($pack->isOk()) {
            $idea = service('idea')->getEntityByEid($pack->getItem('idea_eid'));
            //$link = '<a href="' . route_url('ipar-idea-show', ['zcode' => $idea->zcode]) . '">' . substr($idea->getAbbr(), 0, 21) . '</a>';
            $link = $this->generateLink('idea', $idea->zcode, substr($idea->getAbbr(), 0, 21));
            $pack->addItem('html_message', trans('created-solution-idea') . ', ' . $link);
        }
        return $pack;
    }

    public function saveProductPost()
    {
        $pack = $this->service('rqt')->createSproduct(
            $this->request->request->get('rqt_eid'),
            $this->request->request->get('title'),
            $this->request->request->get('content')
        );

        if ($pack->isOk()) {
            $this->packHtmlMessage($pack, 'create-solution-product', $pack->getItem('product_eid'));
        }
        return $pack;
    }

    public function saveSolutionPost()
    {
        $dst_eid = $this->request->request->get('dst_eid');
        $pack = $this->service('rqt')->createSolution(
            $this->request->request->get('src_eid'),
            $this->request->request->get('dst_type_key'),
            $dst_eid
        );

        if ($pack->isOk()) {
            //$link = $this->getEntityLink($dst_eid);
            $this->packHtmlMessage($pack, 'recommend-solution', $dst_eid);
        }
        return $pack;
    }

    protected function packHtmlMessage($pack, $action, $eid)
    {
        $entity = service('entity')->getEntityByEid($eid);
        $type_key = $entity->getTypeKey();
        $zcode = $entity->zcode;
        $title = $entity->title;
        $link = '<a href="' . route_url("ipar-$type_key-show", ['zcode' => $zcode]) . '">' . $title . '</a>';
        $pack->addItem('html_message', trans($action) . ', ' . $link);
    }

    protected function getEntityLink($eid)
    {
        $entity = service('entity')->getEntityByEid($eid);
        $type_key = $entity->getTypeKey();
        $zcode = $entity->zcode;
        $title = $entity->title;
        return '<a href="' . route_url("ipar-$type_key-show", ['zcode' => $zcode]) . '">' . $title . '</a>';
    }

    protected function generateLink($type_key, $zcode, $title)
    {
        return '<a href="' . route_url("ipar-$type_key-show", ['zcode' => $zcode]) . '">' . $title . '</a>';
    }
}
