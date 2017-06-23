<?php
namespace Ipar\Ui;

class EntityController extends IparControllerBase {
    public function index()
    {
        $entity_set = $this->service('entity')->getEntitySet();
        return $this->page('entity/index', [
            'entity_set' => $entity_set
        ]);
    }
}
