<?php
namespace Admin\Ui;

class IndexController extends AdminControllerBase {
    public function home()
    {
        $start = $this->request->query->get('start');
        $end = $this->request->query->get('end');

        $story_set = gap_service_manager()->make('story')
            ->search(['status' => null]);

        return $this->page('index/home', [
            'story_set' => $story_set,
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function active()
    {
        $eid = $this->getParam('eid');
        $this->service('entity')->active($eid);
        return $this->gotoRoute('admin-entity');
    }

    public function suspend()
    {
        $eid = $this->getParam('eid');
        $this->service('entity')->suspend($eid);
        return $this->gotoRoute('admin-entity');
    }
    public function delete()
    {
        $eid = $this->getParam('eid');
        $entity = $this->service('entity')->getByEid($eid);
        return $this->page('entity/delete', [
            'entity' => $entity
        ]);
    }
    public function deletePost()
    {
        $eid = $this->getParam('eid');
        $post_eid = $this->request->request->get('eid');
        if ($eid != $post_eid) {
            _debug('error request');
        }
        $this->service('entity')->delete($eid);
        return $this->gotoRoute('admin-entity');
    }

    protected function getMenu()
    {
        return require config()->get('base_dir') . '/main/setting/data/admin/menu.php';
    }
}
