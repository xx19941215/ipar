<?php

namespace Admin\Ui;

class TagRqtController extends AdminControllerBase
{
    protected $entity_tag_service;
    protected $tag_service;

    public function bootstrap()
    {
        $this->entity_tag_service = gap_service_manager()->make('entity_tag');
        $this->tag_service = gap_service_manager()->make('tag');
    }

    public function addTagMultiple()
    {
        $url = $_SERVER['HTTP_REFERER'];
        $rqt = $this->getRqtFromParam();
        return $this->page('rqt/add-tag-multiple', ['rqt' => $rqt, 'url' => $url]);
    }

    public function addTagMultiplePost()
    {
        $rqt = $this->getRqtFromParam();
        $data['uid'] = current_uid();
        $data['eid'] = $rqt->eid;
        $data['entity_type_id'] = $rqt->type_id;
        $url = $this->request->request->get('url');
        $data['titles'] = $this->request->request->get('multiple_title');
        $pack = $this->entity_tag_service->saveTagMultiple($data);
        if (!$pack->isOk()) {
            return $this->page('rqt/add-tag-multiple', [
                'rqt' => $rqt,
                'tag' => arr2dto($data, 'tag'),
                'errors' => $pack->getErrors()
            ]);
        }

        return $this->gotoTargetUrl($url);
    }

    public function searchRqt()
    {
        $page = $this->request->query->get('page');
        $tag_id = $this->getParam('tag_id');
        $tag = $this->tag_service->findOne($tag_id);
        $entity_type_id = 1;
        $rqt_set = $this->service('tag_entity')->search(['tag_id'=>$tag_id, 'entity_type_id' => $entity_type_id])
            ->setCurrentPage($page);
        return $this->page('tag/rqt', ['tag' => $tag, 'entity_set' => $rqt_set]);
    }


    protected function getRqtFromParam()
    {
        $eid = $this->getParam('eid');
        return $this->service('rqt')->getRqtByEid($eid);
    }

}