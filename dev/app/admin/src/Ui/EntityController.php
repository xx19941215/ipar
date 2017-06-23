<?php
namespace Admin\Ui;

class EntityController extends AdminControllerBase {

    public function index()
    {
        if ($order = $this->request->query->get('order')) {
            session()->set('admin-order', $order);
        }
        $order = session()->get('admin-order', 'default');

        $query = $this->request->query->get('query');
        $entity_set = $this->service('entity')->schEntitySet([
            'query' => $query,
            'order' => $order,
            'status' => null
        ]);
        $entity_set->setCurrentPage((int) $this->request->get('page'));
        return $this->page('entity/index', [
            'entity_set' => $entity_set,
            'query' => $query,
        ]);
    }

    public function activate()
    {
        $this->prepareTargetUrl();
        return $this->page('entity/activate', [
            'entity' => $this->getEntityFromParam(),
        ]);
    }

    public function activatePost()
    {
        $param_eid = $this->getParam('eid');
        $post_eid = $this->request->request->get('eid');
        if ($param_eid != $post_eid) {
            die('error request');
        }
        $pack = $this->service('entity')->activateEntity($post_eid);
        if ($pack->isOk()) {
            $entity = $this->getEntityFromParam();
            $type = $entity->getTypeKey();
            return $this->gotoRoute("admin-$type-show", ['eid' => $entity->eid]);
            //return $this->gotoTargetUrl('', route_url('admin-entity'));
        } else {
            return $this->page('entity/activate', [
                'entity' => $this->getEntityFromParam(),
                'errors' => $pack->getErrors()
            ]);
        }
    }

    public function deactivate()
    {
      //  print_R($this->getEntityFromParam());
        $this->prepareTargetUrl();
        return $this->page('entity/deactivate', [
            'entity' => $this->getEntityFromParam(),
        ]);
    }

    public function deactivatePost()
    {
        $param_eid = $this->getParam('eid');
        $post_eid = $this->request->request->get('eid');
        if ($param_eid != $post_eid) {
            die('error request');
        }
        $pack = $this->service('entity')->deactivateEntity($post_eid);
        if ($pack->isOk()) {
            $entity = $this->getEntityFromParam();
            $type_key = $entity->getTypeKey();
            return $this->gotoRoute("admin-$type_key-show", ['eid' => $entity->eid]);
            //return $this->gotoTargetUrl('', route_url('admin-entity'));
        } else {
            return $this->page('entity/deactivate', [
                'entity' => $this->getEntityFromParam(),
                'errors' => $pack->getErrors()
            ]);
        }
    }

    public function delete()
    {
        return $this->page('entity/delete', [
            'entity' => $this->getEntityFromParam(),
        ]);
    }

    public function deletePost()
    {
        $param_eid = $this->getParam('eid');
        $post_eid = $this->request->request->get('eid');
        //$post_e_type_key = $this->request->request->get('type_key');
        //$post_rqt_eid = $this->request->request->get('rqt_eid');
        if ($param_eid != $post_eid) {
            die('error request');
        }
        //$rqt_eid = $this->service('rqt')->getRqtEidByDstId($post_eid);
        $pack = $this->service('entity')->delete($post_eid);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-entity-index');
        }

        /*
        if ($pack->isOk()) {
            if($post_e_type == 'rqt') {
                return $this->gotoRoute('admin-rqt');
            } else if(($post_e_type == 'idea') ||($post_e_type == 'invent') ||($post_e_type == 'product')){
                return $this->gotoRoute('admin-rqt-'.$post_e_type,['eid' => $rqt_eid]);
            } else {
                return $this->gotoRoute('admin-entity');
            }
        } else {
            return $this->page('entity/delete', [
                'entity' => $this->getEntityFromParam(),
                'errors' => $pack->getErrors()
            ]);
        }
         */
    }

    public function submit()
    {
        $entity = $this->getEntityFromParam();
        $submit_set = $this->service('entity')->getSubmitSetByEid($entity->eid);
        return $this->page('entity/submit', [
            'entity' => $entity,
            'submit_set' => $submit_set
        ]);
    }
    /**
     * protected functions
     */

    protected function getEntityFromParam()
    {
        if ($eid = $this->getParam('eid')) {
            return $this->service('entity')->getEntityByEid($eid);
        } else {
            return null;
        }
    }

}
