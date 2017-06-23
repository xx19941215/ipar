<?php
namespace Ipar\Rest;

class EntityController extends \Gap\Routing\Controller
{
    public function search()
    {
        $type_key = isset($_GET['type_key'])?$_GET['type_key']:'';

        $entity_set = $this->service('entity_search')->search([
            'query' => $this->request->query->get('query'),
            'type_key' => $this->request->query->get('type_key'),
            'uid' => $this->request->query->get('uid'),
            'limit' => $this->request->query->get('limit'),
            //'tag_id' => $this->request->query->get('tag_id'),
            //'brand_id' => $this->request->query->get('brand_id')
        ]);

        $entity_set->setCurrentPage($this->request->query->get('page'));

        if ($type_key == 'product') {
            $entity_set = $entity_set->setCountPerPage(6)->getItems();
        } elseif ($type_key == 'rqt') {
            $entity_set = $entity_set->setCountPerPage(5)->getItems();
        }

        $arr = [];

        foreach ($entity_set as $entity) {
            $item = [];
            $type_key = $entity->getTypeKey();
            if (!$title = $entity->getTitle()) {
                $title = "$type_key#{$entity->zcode}";
            }

            $user = user($entity->uid);

            $item['html_user'] = implode([
                '<a href="', $user->getUrl(), '">',
                $entity->uid ? $user->nick : '',
                '</a>',
            ]);

            $item['type_key'] = $type_key;
            $item['title'] = $title;
            $item['url'] = route_url("ipar-$type_key-show", ['zcode' => $entity->zcode]);
            //$item['abbr'] = $entity->getAbbr();
            $item['content'] = $entity->getContent();
            $item['html_last_update'] = time_elapsed_string($entity->changed);

            // $item['like_count'] = $entity->countLike();
            $item['dst_eid'] = $entity->eid;
            $item['is_like'] = $this->service('entity_like')->isLike(['eid'=> $entity->eid]) ? 'liked' : '';
            $item['like_text'] = trans('like');
            $item['like_count'] = $this->
                    service('entity_like')->
                    schEntityLikeSet(['eid' => $entity->eid])
                    ->getItemCount();
            $item['comment_text'] = trans('comment');
            $item['comment_count'] = $entity->countComment();
            $item['improving_count'] = $this->service('product')->countPimproving($entity->eid);
            $item['trans_improving'] = trans('improving');
            if ($imgs = $entity->getImgs()) {
                $item['html_cover'] = '<img src="' . img_src($imgs[0], 'cover') . '">';
            }

            if ($imgs = $entity->getImgs()) {
                $img_count = count($imgs);
                $img_count = $img_count > 2 ? 2 : $img_count;
                $img_arr = [];
                $img_arr[] = '<div class="entity-xs-imgs">';
                for ($i = 0; $i < $img_count; $i++) {
                    $img = $imgs[$i];
                    $img_arr[] = '<img src="' . img_src($img, 'cover') . '">';
                }
                $img_arr[] = '</div>';
                $item['html_imgs'] = implode($img_arr);
            }

            $arr[] = $item;
        }

        return $this->packItem('entity', $arr);
    }

    public function fetchPost()
    {
        $eid = $this->request->request->get('eid');
        $entity = service('entity')->getEntityByEid($eid);
        return $this->packItem('entity', [
            'eid' => $entity->eid,
            'title' => $entity->title,
            'content' => $entity->content,
            'url' => route_url('ipar-' . $entity->getTypeKey() . '-show', ['zcode' => $entity->zcode])
        ]);
    }

    public function suggestPost()
    {
        $search_service = service('entity_search');

        $query = $this->request->request->get('query');
        $filters = explode(',', $this->request->request->get('filter', 'rqt,product,user,article'));

        $rqts = [];
        $products = [];
        $features = [];
        $companys = [];
        $correct = [];

        if (in_array('rqt', $filters)) {
            $rqt_set = $search_service
                ->suggest([
                    'type_key' => 'rqt',
                    'query' => $query
                ])
                ->setCountPerPage(5);

            foreach ($rqt_set->getItems() as $rqt) {
                $rqts[] = [
                    'eid' => $rqt->eid,
                    'title' => $rqt->title,
                    'url' => route_url('ipar-rqt-show', ['zcode' => $rqt->zcode])
                ];
            }
        }

        if (in_array('feature', $filters)) {
            $feature_set = $search_service
                ->suggest([
                    'type_key' => 'feature',
                    'query' => $query
                ])
                ->setCountPerPage(5);

            foreach ($feature_set->getItems() as $feature) {
                $features[] = [
                    'eid' => $feature->eid,
                    'title' => $feature->title,
                    'url' => route_url('ipar-feature-show', ['zcode' => $feature->zcode])
                ];
            }
        }

        if (in_array('product', $filters)) {
            $page_count = count($filters) == 1 ? 10 : 5;
            $product_set = $search_service
                ->suggest([
                    'type_key' => 'product',
                    'query' => $query
                ])
                ->setCountPerPage($page_count);

            foreach ($product_set->getItems() as $product) {
                $products[] = [
                    'eid' => $product->eid,
                    'title' => $product->title,
                    'url' => route_url('ipar-product-show', ['zcode' => $product->zcode])
                ];
            }
        }

        if (in_array('article', $filters)) {
            $article_set = service('article_search')
                ->suggest([
                    'type_key' => 'article',
                    'query' => $query
                ])
                ->setCountPerPage(5);

            foreach ($article_set->getItems() as $article) {
                $articles[] = [
                    'id' => $article->id,
                    'title' => $article->title,
                    'url' => route_url('ipar-article-show', ['zcode' => $article->zcode])
                ];
            }
        }

        if (in_array('user', $filters)) {
            $user_set = service('user_search')
                ->suggest([
                    'type_key' => 'article',
                    'query' => $query
                ])
                ->setCountPerPage(5);

            foreach ($user_set->getItems() as $user) {
                $users[] = [
                    'id' => $user->uid,
                    'nick' => $user->nick,
                    'title' => $user->nick,
                    'url' => route_url('ipar-i-home', ['zcode' => $user->zcode])
                ];
            }
        }

        if (in_array('company', $filters)) {
            if (!empty($query)) {
                $company_set = service('company_search')
                    ->suggest([
                        'type_key' => 'company',
                        'query' => $query
                    ])
                    ->setCountPerPage(5);

                foreach ($company_set->getItems() as $company) {
                    $companys[] = [
                        'id' => $company->gid,
                        'title' => $company->name,
                        'url' => route_url('ipar-ui-company-show', ['zcode' => $company->zcode])
                    ];
                }
            }
        }

        if (empty($rqts) && empty($products) && empty($articles)
            && empty($users) && empty($companys)) {
            $correct = service('all_search')->correctSuggest([
                'query' => $query
            ]);
        }

        if (empty($query)) {
            $hotsuggest = service('all_search')->hotSuggest();
        }

        return $this->packItems([
            'query' => $query,
            'rqts' => $rqts,
            'products' => $products,
            'features' => $features,
            'articles' => isset($articles) ? $articles : '',
            'users' => isset($users) ? $users : '',
            'companys' => isset($companys) ? $companys : '',
            'correct' => isset($correct) ? $correct : '',
            'hotsuggest' => isset($hotsuggest) ? $hotsuggest : ''
        ]);
    }

    public function suggest()
    {
        return 'suggest';
    }
}
