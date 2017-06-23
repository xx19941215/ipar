<?php
namespace Ipar\Search\Service;

use Ipar\Search\DataSet\SearchDataSet as DataSet;

class ArticleSearchService extends IparSearchServiceBase
{
    public function schArticleSet($query = [])
    {
        $xs = new \XS('article');

        $search = $xs->search;
        $q = prop($query, 'query');


        $search->setQuery($q);

        return new DataSet($search, "Ipar\Search\Dto\ArticleSearchDto");
    }

    public function suggest($query = [])
    {
        $xs = new \XS('article');
        $search = $xs->search;

        $search->setQuery("title:" . prop($query, 'query'));

        return new DataSet($search, "Ipar\Search\Dto\ArticleSearchDto");
    }
}
