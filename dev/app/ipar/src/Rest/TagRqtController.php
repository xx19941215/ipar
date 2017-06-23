<?php
namespace Ipar\Rest;

class TagRqtController extends \Gap\Routing\Controller
{

    public function index()
    {
        $page = $this->request->query->get('page');
        $zcode = $this->request->query->get('zcode');
        $entity_type_id = 1;
        $entitys = $this->service('tag_entity')
            ->search(['tag_zcode' => $zcode, 'entity_type_id' => $entity_type_id])
            ->setCurrentPage($page)
            ->setCountPerPage(5)
            ->getItems();

        $arr = $this->getRqtItem($entitys);
        return $this->packItem('entity', $arr);
    }

    protected function getRqtItem($entitys)
    {
        $arr = [];

        foreach ($entitys as $entity) {
            $item = [];
            $type_key = $entity->getTypeKey();
            if (!$title = $entity->getTitle()) {
                $title = "$type_key#{$entity->zcode}";
            }

            $user = user($entity->uid);
            $item['type_key'] = $type_key;
            $item['dst_eid'] = $entity->eid;
            $item['dst_type_id'] = 1;
            $item['title'] = $title;
            $item['url'] = route_url("ipar-{$type_key}-show", ['zcode' => $entity->zcode]);
            $item['content'] = $entity->getContent();
            $item['html_last_update'] = implode([
                '<a href="', $user->getUrl(), '">',
                $entity->uid ? $user->nick : '',
                '</a>',
                trans('last-update'),
                ': ',
                time_elapsed_string($entity->changed)
            ]);
            $item['html_submits'] = trans('%s-submits', $entity->countSubmit());
            if ($imgs = $entity->getImgs()) {
                $item['html_imgs'] = $this->getRqtImgs();
            }

            $abbr = $this->getRqtAbbr($entity, $imgs, $item);

            $item['abbr'] = $abbr;
            $item['comment_text'] = trans('comment');
            $item['comment_count'] = $entity->countComment();
            $item['is_like'] = $this->service('entity_like')->isLike(['eid'=> $entity->eid]) ? 'liked' : '';
            $item['like_text'] = trans('like');
            $item['like_count'] = $entity->countLike();

            $arr[] = $item;
        }
        return $arr;
    }

    protected function getRqtAbbr($entity, $imgs, $item)
    {
        $abbr = $entity->getAbbr();
        if (isset($abbr[97])) {
            $abbr .= '<a class="read-more" href="javascript:;">' . trans('read-more') . '</a>';
        } else {
            if (!$imgs) {
                $abbr = prop($item, 'entity_content', '');
            }
        }
        return $abbr;
    }

    protected function getRqtImgs($imgs = [])
    {
        $img_count = count($imgs);
        $img_count = $img_count > 2 ? 2 : $img_count;
        $img_arr = [];
        $img_arr[] = '<div class="entity-imgs">';
        for ($i = 0; $i < $img_count; $i++) {
            $img = $imgs[$i];
            $img_arr[] = '<img src="' . img_src($img, 'cover') . '">';
        }
        $img_arr[] = '</div>';
        return implode($img_arr);
    }
}