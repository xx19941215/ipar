<?php
namespace Ipar\Search\Service;

use Ipar\Search\DataSet\SearchDataSet as DataSet;

class EntitySearchService extends IparSearchServiceBase
{
    public function search($query = [])
    {

        $xs = new \XS('entity');
        $search = $xs->search;

        $q = prop($query, 'query');
        if ($type_key = prop($query, 'type_key')) {
            $q .= " type_id:" . get_type_id($type_key);
        }
        if ($uid = (int) prop($query, 'uid')) {
            $q .= " uid:$uid";
        }

        $status = prop($query, 'status', 1);
        if ($status !== null) {
            $q .= " status:$status";
        }

        $search->setQuery($q);

        return new DataSet($search, "Ipar\Search\Dto\EntitySearchDto");
    }

    public function suggest($query = [])
    {
        $xs = new \XS('entity');
        $search = $xs->search;

        $q = "title:" . prop($query, 'query');
        if ($type_key = prop($query, 'type_key')) {
            $q .= " type_id:" . get_type_id($type_key);
        }
        if ($uid = (int) prop($query, 'uid')) {
            $q .= " uid:$uid";
        }

        $status = prop($query, 'status', 1);
        if ($status !== null) {
            $q .= " status:$status";
        }

        $search->setQuery($q);

        return new DataSet($search, "Ipar\Search\Dto\EntitySearchDto");
    }

    public function countComment($eid)
    {
        return $this->countComment($eid);
    }
}
