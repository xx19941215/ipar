<?php
namespace Ipar\Search\Service;

use Ipar\Search\DataSet\SearchDataSet as DataSet;

class AllSearchService extends IparSearchServiceBase
{
    public function correctSuggest($query, $filter = ['article', 'entity', 'company'])
    {
        $res = [];
        $arr = [];
        $baseUrl = route_url('home') . 'search?query=';

        $filerLimitArr = $this->getFilterLimitArr();

        foreach ($filter as $f) {
            if (!isset($filerLimitArr[$f])) {
                return 'error';
            }
            $xs = new \XS($f);
            $search = $xs->search;
            $search->setQuery(prop($query, 'query'));
            $corrected[$f] = $search->getCorrectedQuery();
        }

        foreach ($corrected as $corr) {
            $res = array_merge($res, $corr);
        }
        $res = array_unique($res);

        foreach ($res as $row => $key) {
            $url = $baseUrl . $key;
            $arr[] = [
                'url' => $url,
                'title' => $key
            ];
        }

        return $arr;
    }

    public function hotSuggest($filter = ['article', 'entity', 'company'])
    {
        $baseUrl = route_url('home') . 'search?query=';
        if ($res = $this->cache()->get('hot-suggest-words')) {
            $res = json_decode($res);
            foreach ($res as $row) {
                $row->url = $baseUrl . $row->url;
            }
            return $res;
        }

        $res = [];
        $arr = [];

        $filerLimitArr = $this->getFilterLimitArr();

        foreach ($filter as $f) {
            if (!isset($filerLimitArr[$f])) {
                return 'error';
            }
            $xs = new \XS($f);
            $search = $xs->search;
            $corrected[$f] = $search->getHotQuery(10, 'lastnum');
        }

        foreach ($corrected as $corr) {
            $res = array_merge($res, $corr);
        }
        $res = array_unique($res);

        foreach ($res as $row => $key) {
            $url = $row;
            $arr[] = [
                'url' => $url,
                'title' => $row
            ];
        }

        $this->cache()->set('hot-suggest-words', json_encode(array_slice($arr, 0, 10)), 3600);

        foreach ($arr as &$row) {
            $row['url'] = $baseUrl . $row['url'];
        }

        return array_slice($arr, 0, 10);
    }

    public function relatedSuggest($query, $filter = ['article', 'entity', 'company'])
    {
        $res = [];
        $arr = [];
        $baseUrl = route_url('home') . 'search?query=';

        $filerLimitArr = $this->getFilterLimitArr();

        foreach ($filter as $f) {
            if (!isset($filerLimitArr[$f])) {
                return 'error';
            }
            $xs = new \XS($f);
            $search = $xs->search;
            $corrected[$f] = $search->getRelatedQuery(prop($query, 'query'), 10);
        }

        foreach ($corrected as $corr) {
            $res = array_merge($res, $corr);
        }
        $res = array_unique($res);

        foreach ($res as $row => $key) {
            $url = $baseUrl . $key;
            $arr[] = [
                'url' => $url,
                'title' => $key
            ];
        }

        return $arr;
    }

    protected function getFilterLimitArr()
    {
        return [
            'article' => 1,
            'entity' => 1,
            'company' => 1
        ];
    }
}
