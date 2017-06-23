<?php
namespace Ipar\Ui;

class IndexController extends IparControllerBase {
    public function home() {
        $activity_advice_banner_url = '';

        $activity_date = $this->service('activity')->getActivityDate();
        $date = date('Y-m-d H:i:s');
        if ($activity_date && (strtotime($date) - strtotime($activity_date->created) > 0))
        {
            $activity_advice_banner_url = $this->service('activity')->getActivityIndexImg();
        }

        return $this->page('index/home', [
            'activity_advice_banner_url' => $activity_advice_banner_url
        ]);
    }

    public function search()
    {
        return $this->page('index/search', [
            'query' => $this->request->query->get('query'),
            'type' => 'all'
        ]);
    }
}
