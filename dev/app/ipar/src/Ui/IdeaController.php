<?php
namespace Ipar\Ui;

class IdeaController extends IparControllerBase {
    public function index()
    {
        $idea_service = $this->service('idea');
        $page_index = (int) $this->request->query->get('page');
        $page_index = $page_index ? $page_index : 1;
        $rs = $idea_service->getActiveEntities($page_index);
        return $this->page('idea/index', [
            'page_count' => $rs['page_count'],
            'page_index' => $rs['page_index'],
            'ideas' => $rs['entities']
        ]);
    }

    public function create()
    {
        $src_eid = (int) $this->request->query->get('src_eid');
        $rqt = $this->service('story')->getEtByEid($src_eid);
        if (!$rqt) {
            _debug('todo');
        }
        $rqt->desc = '';
        return $this->page('idea/create', [
            'rqt' => $rqt,
        ]);
    }

    public function createPost()
    {
        $idea_service = $this->service('idea');

        $post = $this->request->request;
        $src_eid = $post->get('src_eid');

        if ($idea_service->createIdea(
            $this->request->getUid(),
            $src_eid,
            $post->get('content')
        )) {
            $rqt = $this->service('story')->getEtByEid($src_eid);
            return $this->gotoRoute(
                'rqt-show',
                ['zcode' => $rqt->zcode]
            );
        } else {
            $data = $post->all();
            $data['errors'] = $idea_service->getErrors();
            return $this->page('idea/create', $data);
        }
    }

    public function show()
    {
        $zcode = $this->getParam('zcode');
        $idea_service = $this->service('idea');
        $idea = $idea_service->getIdeaByZcode($zcode);
        $rqt = $idea_service->getRqtByIdeaEid($idea->eid);
        return $this->page('idea/show',[
            'idea' => $idea,
            'rqt' => $rqt
        ]);
    }

    public function edit()
    {
        $zcode = $this->getParam('zcode');
        $idea = $this->service('story')->getEpByZcode('idea', $zcode);
        $data = (array) $idea;
        return $this->page(
            'ipar/idea/edit',
            $data
        );
    }

    public function editPost()
    {
        $idea_service = $this->service('idea');

        $post = $this->request->request;

        $uid = $this->request->getUid();
        $eid = $post->get('eid');
        $content = $post->get('content');

        if ($idea_service->editIdea(
            $uid,
            $eid,
            $content
        )) {
            return $this->gotoRoute(
                'idea-show',
                ['zcode' => $post->get('zcode')]
            );
        } else {
            $data = $post->all();
            $data['errors'] = $idea_service->getErrors();
            return $this->page('idea/edit', $data);
        }
    }

    public function delete()
    {
        $zcode = $this->getParam('zcode');
        $idea = $this->service('idea')->getEntityByZcode($zcode);

        return $this->page('idea/delete', ['idea' => $idea]);
    }

    public function deletePost()
    {
        $zcode = $this->getParam('zcode');
        $idea = $this->service('idea')->getEntityByZcode($zcode);
        if ($idea->eid == $this->request->request->get('eid')) {
            $this->service('idea')->remove($idea->eid);
            return $this->gotoRoute('idea-index');
        } else {
            die(trans('unkown-error'));
        }
    }

}
