<?php
namespace Ipar\Rest;

class FeatureController extends \Gap\Routing\Controller {
    public function savePost()
    {
        $feature_service = service('feature');

        $eid = (int) $this->request->request->get('eid');
        $title = $this->request->request->get('title');
        $content = $this->request->request->get('content');

        if ($eid > 0) {
            $pack = $feature_service->updateFeature($eid, $title, $content);
            if ($pack->isOk()) {
                $pack->addItem('eid', $eid);
                $pack->addItem('title', $title);
                $pack->addItem('content', $content);
            }
            return $pack;
        }

        $pack = $feature_service->createFeature($title, $content);
        if ($pack->isOk()) {
            $feature = $feature_service->getEntityByEid($pack->getItem('eid'));
            $link = '<a href="' . route_url('ipar-feature-show', ['zcode' => $feature->zcode]) . '">' . $feature->title . '</a>';
            $pack->addItem('html_message', trans('created-feature') . ', ' . $link);
        }
        return $pack;
    }

    public function product()
    {
        $product_set = service('feature')->schFproductSet(
            $this->request->query->get('eid')
        );
        $product_set->setCurrentPage($this->request->query->get('page'));

        $arr = [];

        foreach ($product_set->getItems() as $entity) {
            $item = [];
            $type = $entity->getTypeKey();
            if (!$title = $entity->getTitle()) {
                $title = "$type#{$entity->zcode}";
            }

            $user = user($entity->uid);

            $item['type'] = $type;
            $item['title'] = $title;
            $item['url'] = route_url("ipar-{$type}-show", ['zcode' => $entity->zcode]);
            $item['abbr'] = $entity->getAbbr();
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

            $arr[] = $item;
        }
        return $this->packItem('product', $arr);
    }
}
