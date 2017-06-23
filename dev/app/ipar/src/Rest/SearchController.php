<?php
namespace Ipar\Rest;

class SearchController extends \Gap\Routing\Controller
{
    public function relatedSuggest()
    {
        $query = $this->request->query->get('query');
        if ($query) {
            $temp_asso_suggest = $this->service('all_search')->relatedSuggest(['query' => $query]);
            shuffle($temp_asso_suggest);
            $res['associate_suggest'] = array_slice($temp_asso_suggest, 0, 5);
            $temp_hot_suggest = service('all_search')->hotSuggest();
            shuffle($temp_hot_suggest);
            $res['hot_suggest'] = array_slice($temp_hot_suggest, 0, 5);
            return $this->packItems($res);
        }
        return;
    }
}
