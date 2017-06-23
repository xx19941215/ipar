<?php
namespace Ipar\Search\Service;

use Ipar\Search\DataSet\SearchDataSet as DataSet;

class CompanySearchService extends IparSearchServiceBase
{
    public function schCompanySet($query = [])
    {
        $xs = new \XS('company');

        $search = $xs->search;
        $q = prop($query, 'query');


        $search->setQuery($q);

        return new DataSet($search, "Ipar\Search\Dto\CompanySearchDto");
    }

    public function suggest($query = [])
    {
        $xs = new \XS('company');
        $search = $xs->search;

        $search->setQuery(prop($query, 'query'));

        return new DataSet($search, "Ipar\Search\Dto\CompanySearchDto");
    }
}
