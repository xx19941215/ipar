<?php
namespace Ipar\Rest;

class ArticleController extends \Gap\Routing\Controller
{
    public function search()
    {

        $article_set = $this->service('article_search')->schArticleSet([
            'query' => $this->request->query->get('query')
        ]);

        $article_set->setCountPerPage(5)->setCurrentPage($this->request->query->get('page'));

        $arr = [];

        foreach ($article_set->getItems() as $article) {
            $item = [];
            if (!$title = $article->getTitle()) {
                $title = "{$article->zcode}";
            }

            $user = user($article->uid);

            $item['title'] = $title;
            $item['url'] = route_url("ipar-article-show", ['zcode' => $article->zcode]);
            $item['content'] = $article->getContent();
            $item['user'] = $user;
            $item['user_url'] = $user->getUrl();
            $item['user_nick'] = $user->nick;
            $item['avt_url'] = img_src($user->avt, 'small');

            if ($avt = $user->getAvt()) {
                $item['user_avt'] = '<img src="' . img_src($avt, 'small') . '">';
            } else {
                $item['user_avt'] = '<i class="icon icon-avt"></i>';
            }

            $item['nick'] = $user->nick;
            $item['html_last_update'] = time_elapsed_string($article->changed);
            $item['created'] = time_elapsed_string($article->changed);

            $arr[] = $item;
        }

        return $this->packItem('article', $arr);
    }

    public function fetchPost()
    {
        $eid = $this->request->request->get('eid');
        $article = service('article')->getArticleByEid($eid);
        return $this->packItem('article', [
            'eid' => $article->eid,
            'title' => $article->title,
            'content' => $article->content,
            'url' => route_url('ipar-' . $article->getTypeKey() . '-show', ['zcode' => $article->zcode])
        ]);
    }

    public function suggestPost()
    {
        $search_service = service('article_search');

        $query = $this->request->request->get('query');
        $filters = explode(',', $this->request->request->get('filter', 'rqt,product'));

        $rqts = [];
        $products = [];
        $features = [];

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
            $product_set = $search_service
                ->suggest([
                    'type_key' => 'product',
                    'query' => $query
                ])
                ->setCountPerPage(5);

            foreach ($product_set->getItems() as $product) {
                $products[] = [
                    'eid' => $product->eid,
                    'title' => $product->title,
                    'url' => route_url('ipar-product-show', ['zcode' => $product->zcode])
                ];
            }

        }

        return $this->packItems([
            'query' => $query,
            'rqts' => $rqts,
            'products' => $products,
            'features' => $features
        ]);
    }

    public function suggest()
    {
        return 'suggest';
    }

    public function index()
    {
        $article_set = $this->service('article')->getArticleSet();
        $article_set->setCurrentPage($this->request->query->get('page'));
        $article_set = $article_set->getItems();
        $arr = [];

        foreach ($article_set as $article) {
            $user = user($article->uid);
            $item = [];
            $item['title'] = $article->title;
            $item['url'] = route_url('ipar-article-show', ['zcode' => $article->zcode]);
            $abbr = $article->getAbbr();
            if (isset($abbr[100])) {
                $abbr .= '<a class="read-more" href=' . $item["url"] . '>' . trans('read-more') . '</a>';
            }
            $item['abbr'] = $abbr;
            $item['user_nick'] = isset(user($article->uid)->nick) ? user($article->uid)->nick : '';
            $item['created'] = time_elapsed_string($article->created);
            $item['user_url'] = $user->getUrl();

            if ($imgs = $article->getImgs()) {
                $img_count = count($imgs);
                $img_count = $img_count > 2 ? 2 : $img_count;
                $img_arr = [];
                $img_arr[] = '<div class="entity-imgs">';
                for ($i = 0; $i < $img_count; $i++) {
                    $img = $imgs[$i];
                    $img_arr[] = '<img src="' . img_src($img, 'cover') . '">';
                }
                $img_arr[] = '</div>';
                $item['html_article_imgs'] = implode($img_arr);
            }

            $arr[] = $item;
        }
        return $this->packItem('article', $arr);
    }

}
