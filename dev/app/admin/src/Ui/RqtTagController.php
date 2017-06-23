<?php

namespace Admin\Ui;

class RqtTagController extends AdminControllerBase
{
    protected $entity_tag_service;

    public function bootstrap()
    {
        $this->entity_tag_service = gap_service_manager()->make('entity_tag');
    }


    public function search()
    {
        $rqt = $this->getRqtFromParam();
        $query = $this->request->query->get('query', '');
        $data['eid'] = $rqt->eid;
        $data['entity_type_id'] = $rqt->type_id;
        $data['query'] = $query;
        $tag_set = $this->entity_tag_service->search($data);
        return $this->page('rqt/tag', [
            'rqt' => $rqt,
            'tag_set' => $tag_set,
        ]);
    }

    public function addTag()
    {
        $rqt = $this->getRqtFromParam();
        return $this->page('rqt/add-tag', ['rqt' => $rqt]);
    }

    public function addTagPost()
    {
        $rqt = $this->getRqtFromParam();
        $title = $this->request->request->get('title');
        $data['tag_title'] = $title;
        $data['tag_content'] = $this->request->request->get('content');
        $data['uid'] = current_uid();
        $data['eid'] = $rqt->eid;
        $data['entity_type_id'] = $rqt->type_id;
        $pack = $this->entity_tag_service->save($data);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-rqt_tag-search', ['eid' => $rqt->eid]);

        }
        return $this->page('rqt/add-tag', [
            'rqt' => $rqt,
            'tag' => arr2dto($data, 'tag'),
            'errors' => $pack->getErrors()
        ]);
    }

    public function unlink()
    {
        $tag_id = $this->getParam('tag_id');
        $rqt = $this->getRqtFromParam();
        $tag = gap_service_manager()->make('tag')->findOne($tag_id);
        return $this->page('rqt/unlink', ['rqt' => $rqt, 'tag' => $tag]);
    }

    public function unlinkPost()
    {
        $eid = $this->request->request->get('eid');
        $tag_id = $this->request->request->get('tag_id');
        $pack = $this->entity_tag_service->delete(['eid' => $eid, 'tag_id' => $tag_id]);
        if (!$pack->isOk()) {
            return $this->page('rqt/unlink', ['errors' => $pack->getErrors()]);
        }
        return $this->gotoRoute('admin-ui-rqt_tag-search', ['eid' => $eid]);
    }

    protected function getRqtFromParam()
    {
        $eid = $this->getParam('eid');
        return $this->service('rqt')->getRqtByEid($eid);
    }

}
