<?php
namespace Ipar\Rest;

use Gap\Routing\Controller as RoutingController;

class StoryController extends RoutingController {
    public function index()
    {
        $query = [
            'type_key' => $this->request->query->get('type_key'),
            'tag_id' => $this->request->query->get('tag_id'),
            'eid' => $this->request->query->get('eid'),
            'uid' => $this->request->query->get('uid'),
            'sort' => $this->request->query->get('sort')
        ];
        // print_r($query);exit;
        $stories = gap_service_manager()->make('story')
            ->search($query)
            ->setCurrentPage($this->request->query->get('page'))
            ->getItems();

        $arr = [];

        foreach ($stories as $story) {
            $dst_type_key = get_type_key($story->dst_type_id);
            $src_type_key = get_type_key($story->src_type_id);
            $entity_type_id = $story->dst_type_id;
            if ($dst_type_key == 'improving' || $dst_type_key == 'solved') {
                $entity_type_key = 'rqt';
                $entity_type_id = 1;
            } else {
                $entity_type_key = $dst_type_key;
            }

            $user = user($story->uid);

            if (!$user) {
                continue;
            }

            $item = [];

            $item['dst_eid'] = $story->dst_eid;

            $item['dst_type_key'] = $dst_type_key;
            $item['dst_type_id'] = $entity_type_id;
            $item['entity_type_key'] = $entity_type_key;
            $item['user_nick'] = $user->nick;
            $item['user_url'] = $user->getUrl();
            if ($avt = $user->getAvt()) {
                $item['user_avt'] = '<img src="' . img_src($avt, 'small') . '">';
            } else {
                $item['user_avt'] = '<i class="icon icon-avt"></i>';
            }

            $src_url = route_url("ipar-$src_type_key-show", ['zcode' => $story->src_zcode]);
            $dst_url = route_url("ipar-$entity_type_key-show", ['zcode' => $story->dst_zcode]);
            $item['entity_url'] = $dst_url;
            /*
            if($entity_type_key == 'product') {
                $item['entity_url'] = route_url("ipar-$entity_type_key-improving", ['zcode' => $story->dst_zcode]);
            }
             */
            if ($entity_title = $story->getDataTitle()) {
                $item['entity_title'] = $entity_title;
            } else {
                $item['entity_title'] = "$dst_type_key#{$story->dst_zcode}";
            }

            $item['entity_content'] = $story->getDataContent();
            if ($story->src_eid) {
                $item['html_story_src'] = implode([
                    '<div class="story-src">',
                    '<span class="label">',
                    trans("src-$src_type_key"),
                    ', </span>',
                    '<a class="title" href="', $src_url, '">',
                    $story->src_title,
                    '</a>',
                    '</div>'
                ]);
            } else {
                $item['html_story_src'] = '';
            }

            if ($imgs = $story->getImgs()) {
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

            $abbr = $story->getDataAbbr();
            if (isset($abbr[97])) {
                $abbr .= '...<a class="read-more" href="javascript:;">' . trans('read-more') . '</a>';
            } else {
                if (!$imgs) {
                    $abbr = $item['entity_content'];
                }
            }

            $item['entity_abbr'] = $abbr;

            $item['comment_text'] = trans('comment');
            $item['comment_count'] = $story->countComment();
            $item['is_like'] = $this->service('entity_like')->isLike(['eid'=> $story->dst_eid]) ? 'liked' : '';
            $item['like_text'] = trans('like');
            $item['like_count'] = $this->service('entity_like')->schEntityLikeSet(['eid' => $story->dst_eid])->getItemCount();

            $item['time_elapsed'] = time_elapsed_string($story->created);
            $item['action_text'] = trans($story->getActionKey() . '-' . $dst_type_key);
            $arr[] = $item;
        }
        return $this->packItem('story', $arr);
    }
}
