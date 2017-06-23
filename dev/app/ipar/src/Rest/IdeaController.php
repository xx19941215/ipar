<?php
namespace Ipar\Rest;

class IdeaController extends \Gap\Routing\Controller {
    public function savePost()
    {
        $eid = (int) $this->request->request->get('eid');
        $rqt_eid = (int) $this->request->request->get('rqt_eid');
        $content = $this->request->request->get('content');

        if ($eid > 0) {
            $pack = service('idea')->updateIdea($eid, $content);
            if ($pack->isOk()) {
                $pack->addItem('eid', $eid);
                $pack->addItem('content', $content);
            }
            return $pack;
        }

        return service('rqt')->createSidea($rqt_eid, $content);
    }
}
