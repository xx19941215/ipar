<?php
namespace Ipar\Rest;

class TagArticleController extends \Gap\Routing\Controller
{

    public function index()
    {
        $page = $this->request->query->get('page');
        $zcode = $this->request->query->get('zcode');
        $articles = $this->service('tag_article')
            ->search(['tag_zcode' => $zcode])
            ->setCurrentPage($page)
            ->setCountPerPage(4)
            ->getItems();

        $arr = $this->getArticleItem($articles);
        return $this->packItem('article', $arr);
    }

    protected function getArticleItem($articles)
    {
        $arr = [];
        foreach ($articles as $article) {
            $item = [];
            $user = user($article->uid);
            $item['url'] = route_url('ipar-article-show', ['zcode' => $article->zcode]);
            $item['title'] = $article->title;

            $item['abbr'] = $article->getAbbr();

            $item['user_url'] = $user->getUrl();
            $item['user_nick'] = $user->nick;
            $item['created'] = time_elapsed_string($article->created);
            $arr[] = $item;
        }
        return $arr;
    }

}