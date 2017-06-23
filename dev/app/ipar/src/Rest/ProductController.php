<?php
namespace Ipar\Rest;

class ProductController extends \Gap\Routing\Controller
{
    protected $product_service;

    public function bootstrap()
    {
        $this->product_service = $this->service('product');
    }

    public function index()
    {
        $query = [
            'type_key' => $this->request->query->get('type_key'),
            'tag_id' => $this->request->query->get('tag_id'),
            'eid' => $this->request->query->get('eid'),
            'uid' => $this->request->query->get('uid'),
            'sort' => $this->request->query->get('sort')
        ];

        $products = $this->product_service
            ->schProductSet($query)
            ->setCountPerPage(12)
            ->setCurrentPage($this->request->query->get('page'))
            ->getItems();

        $arr = [];

        foreach ($products as $product) {
            $item = [];
            $user = user($product->uid);
            if (!$user) {
                continue;
            }

            //$item['type_key'] = $product->getTypeKey();
            $item['zcode'] = $product->zcode;
            $item['title'] = title_text($product->getTitle(), 25);
            if ($imgs = $product->getImgs()) {
                $item['html_cover'] = '<img src="' . img_src($imgs[0], 'cover') . '">';
            }
            $item['url'] = route_url('ipar-product-show', ['zcode' => $product->zcode]);

            $item['html_user'] = implode([
                '<a href="', $user->getUrl(), '">',
                $product->uid ? $user->nick : '',
                '</a>',
            ]);

            $item['like_count'] = $product->countLike();
            $item['dst_eid'] = $product->eid;
            $item['is_like'] = $this->service('entity_like')->isLike(['eid' => $product->eid]) ? 'liked' : '';
            $item['like_text'] = trans('like');
            $item['like_count'] = $this->service('entity_like')->schEntityLikeSet(['eid' => $product->eid])->getItemCount();
            $item['improving_count'] = $this->service('product')->countPimproving($product->eid);
            $item['trans_improving'] = trans('improving');

            $arr[] = $item;
        }
        return $this->packItem('product', $arr);
    }

    public function property()
    {
        $eid = $this->request->query->get('eid');
        if (!$eid) {
            return [];
        }

        $ptype_key = $this->request->query->get('ptype_key');
        $properties = $this->product_service->schPropertySet($eid, [
            'ptype_key' => $ptype_key
        ])
            ->setCurrentPage($this->request->query->get('page'))
            ->getItems();

        $arr = [];

        foreach ($properties as $property) {
            $type_key = $property->getTypeKey();
            if (!$title = $property->getTitle()) {
                $title = "$type_key#{$property->zcode}";
            }
            if (!$property->eid) {
                continue;
            }
            $url = route_url("ipar-$type_key-show", ['zcode' => $property->zcode]);
            $item = [
                'id' => $property->id,
                'url' => $url,
                'ptype_key' => get_type_key($property->ptype_id),
                'type_key' => $type_key,
                'zcode' => $property->zcode,
                'title' => $title,
                'read_more' => trans('read-more'),
                'content' => $property->getContent(),
                'html_last_update' => implode([
                    '<a class="entity-info-user-nick" href="javascript:;">',
                    $property->uid ? user($property->uid)->nick : '',
                    '</a>',
                    trans('last-update'),
                    ': ',
                    time_elapsed_string($property->changed)
                ]),
                'html_submits' => trans('%s-submits', $property->countSubmit()),
                'dst_eid' => $property->eid,
                'dst_type_id' => $property->type_id
            ];

            if ($imgs = $property->getImgs()) {
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

            $abbr = $property->getAbbr();
            if (isset($abbr[97])) {
                $abbr .= '<a class="read-more" href="javascript:;">' . trans('read-more') . '</a>';
            } else {
                if (!$imgs) {
                    $abbr = $item['content'];
                }
            }

            $item['abbr'] = $abbr;

            $item['comment_text'] = trans('comment');
            $item['comment_count'] = $property->countComment();
            $item['is_voted'] = $this->service('property')->isVoted($property->id, current_uid()) ? 'voted' : '';

            $item['vote_text'] = trans('vote');
            $item['vote_count'] = $this->service('property_vote')->countVoteUser($property->id);

            $arr[] = $item;
        }


        return $this->packItem('property', $arr);
    }

    public function savePost()
    {
        $product_service = $this->product_service;

        $eid = (int)$this->request->request->get('eid');
        $title = $this->request->request->get('title');
        $url = $this->request->request->get('url');
        $content = $this->request->request->get('content');

        if ($eid > 0) {
            $pack = $product_service->updateProduct($eid, $title, $content);
            if ($pack->isOk()) {
                $pack->addItem('eid', $eid);
                $pack->addItem('title', $title);
                $pack->addItem('content', $content);
            }
            return $pack;
            //return $product_service->updateProduct($eid, $title, $content, $url);
        }

        $pack = $product_service->createProduct($title, $content, $url);
        if ($pack->isOk()) {
            $product = $product_service->getEntityByEid($pack->getItem('eid'));
            $link = '<a href="' . route_url('ipar-product-show', ['zcode' => $product->zcode]) . '">' . $product->title . '</a>';
            $pack->addItem('html_message', trans('created-product') . ', ' . $link);
        }
        return $pack;

    }

    public function savePropertyPost()
    {
        $dst_eid = $this->request->request->get('dst_eid');
        $dst_type_key = $this->request->request->get('dst_type_key');

        $pack = $this->product_service->createProperty(
            $this->request->request->get('src_eid'),
            $dst_type_key,
            $dst_eid
        );

        if ($pack->isOk()) {
            $this->packHtmlMessage($pack, 'recommend-property-' . $dst_type_key, $dst_eid);
        }
        return $pack;
    }

    public function saveFeaturePost()
    {
        $pack = $this->product_service->createPfeature(
            $this->request->request->get('product_eid'),
            $this->request->request->get('title'),
            $this->request->request->get('content')
        );
        if ($pack->isOk()) {
            $this->packHtmlMessage($pack, 'add-property-feature', $pack->getItem('feature_eid'));
        }
        return $pack;
    }

    public function saveSolvedPost()
    {
        $pack = $this->product_service->createPsolved(
            $this->request->request->get('product_eid'),
            $this->request->request->get('title'),
            $this->request->request->get('content')
        );
        if ($pack->isOk()) {
            $this->packHtmlMessage($pack, 'add-property-solved', $pack->getItem('rqt_eid'));
        }
        return $pack;
    }

    public function saveImprovingPost()
    {
        $pack = $this->product_service->createPimproving(
            $this->request->request->get('product_eid'),
            $this->request->request->get('title'),
            $this->request->request->get('content')
        );
        if ($pack->isOk()) {
            $this->packHtmlMessage($pack, 'add-property-improving', $pack->getItem('rqt_eid'));
        }
        return $pack;
    }

    public function saveRqtPost()
    {
        $type_key = $this->request->request->get('type_key');
        if ($type_key === 'solved') {
            return $this->product_service->createPsolved(
                $this->request->request->get('product_eid'),
                $this->request->request->get('title'),
                $this->request->request->get('content')
            );
        }
        if ($type_key === 'improving') {
            return $this->product_service->createPimproving(
                $this->request->request->get('product_eid'),
                $this->request->request->get('title'),
                $this->request->request->get('content')
            );
        }
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
}
