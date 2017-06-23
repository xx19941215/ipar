<?php
namespace Ipar\Search\Service;

use Ipar\Search\DataSet\SearchDataSet as DataSet;

class UserSearchService extends IparSearchServiceBase
{
    public function schUserSet($query = [])
    {
        $xs = new \XS('user');

        $search = $xs->search;
        $q = prop($query, 'query');

        $search->setQuery($q);

        return new DataSet($search, "Ipar\Search\Dto\UserSearchDto");
    }

    public function suggest($query = [])
    {
        $xs = new \XS('user');
        $search = $xs->search;

        $search->setQuery("nick:" . prop($query, 'query'));

        return new DataSet($search, "Ipar\Search\Dto\UserSearchDto");
    }

}
