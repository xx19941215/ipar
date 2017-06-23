<?php
namespace Admin\Ui;

class TagController extends AdminControllerBase
{
    protected $tag_service;

    public function bootstrap()
    {
        $this->tag_service = gap_service_manager()->make('tag');
    }

    public function index()
    {
        $page = $this->request->query->get('page');
        $search = $this->request->query->get('query');

        $tag_set = $this->tag_service->search(['search' => $search])->setCurrentPage($page);
        return $this->page('tag/index', [
            'tag_set' => $tag_set
        ]);
    }

    public function show()
    {
        $tag_id = $this->getParam('tag_id');
        $tag_set = $this->tag_service->findOne($tag_id);
        return $this->page('tag/show', ['tag' => $tag_set]);
    }

    public function add()
    {
        return $this->page('tag/add');
    }

    public function addPost()
    {
        $data = $this->getRequestData();
        if ($img_file = $this->request->files->get('logo')) {
            $data['logo'] = json_encode($this->uploadLogo($img_file));
        }
        $pack = $this->tag_service->create($data);
        if ($pack->isOK()) {
            return $this->gotoRoute('admin-ui-tag-show', ['tag_id' => $pack->getItem('id')]);
        }
        $data['errors'] = $pack->getErrors();
        return $this->page('tag/add', $data);
    }

    public function delete()
    {
        $tag_id = $this->getParam('tag_id');

    }

    public function deletePost()
    {

    }

    public function edit()
    {
        $tag_id = $this->getParam('tag_id');
        $tag = $this->tag_service->findOne($tag_id);
        return $this->page('tag/edit', (array)$tag);
    }

    public function editPost()
    {
        $tag_id = $this->getParam('tag_id');
        $post_id = $this->request->request->get('id');
        if ($tag_id != $post_id) {
            die('error request');
        }
        $data = $this->getRequestData();
        if ($img_file = $this->request->files->get('logo')) {
            $data['logo'] = json_encode($this->uploadLogo($img_file));
        }
        $pack = $this->tag_service->update(['id' => $tag_id], $data);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-tag-index');
        }
        $data['errors'] = $pack->getErrors();
        return $this->page('tag/edit', $data);
    }

    public function activate()
    {
        $tag_id = $this->getParam('tag_id');
        $tag = $this->tag_service->findOne($tag_id);
        return $this->page('tag/activate', ['tag' => $tag]);
    }

    public function activatePost()
    {
        $tag_id = $this->request->request->get('id');
        $pack = $this->tag_service->activateTagById($tag_id);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-tag-index');
        }
    }

    public function deactivate()
    {
        $tag_id = $this->getParam('tag_id');
        $tag = $this->tag_service->findOne($tag_id);
        return $this->page('tag/deactivate', ['tag' => $tag]);
    }

    public function deactivatePost()
    {
        $tag_id = $this->request->request->get('id');
        $pack = $this->tag_service->deactivateTagById($tag_id);
        if ($pack->isOk()) {
            return $this->gotoRoute('admin-ui-tag-index');
        }
    }


    protected function getRequestData()
    {
        $post = $this->request->request;
        return [
            'id' => $post->get('id'),
            'title' => $post->get('title'),
            'content' => $post->get('content'),
            'uid' => $post->get('uid')
        ];
    }

    protected function uploadLogo($img_file)
    {
        $img_tool = image_tool();
        $pack = $img_tool->save($img_file);

        if (!$pack->isOk()) {
            return $pack->toArray();
        }

        $image = $pack->getItem('image');
        $image->resize('small', ['w' => 100]);
        $image->resize('large', ['w' => 600]);
        $image->resize('cover', ['w' => 200, 'h' => 123]);

        return [
            'site' => config()->get('img.site'),
            'dir' => $image->dir,
            'name' => $image->name,
            'ext' => $image->ext
        ];
    }

    public function logoUpload()
    {
        $tag_id = $this->getParam('tag_id');
        $tag = $this->tag_service->findOne($tag_id);
        return $this->page('tag/upload_logo', [
            'tag' => $tag
        ]);
    }

    public function logoUploadPost()
    {
        if (!$img_file = $this->request->files->get('logo')) {
            die('Empty Logo file! Please select one!');
        }

        $data = $this->getRequestData();
        $logo = json_encode($this->uploadLogo($img_file));
        $pack = $this->service('tag')->updateField(['id' => $data['id']], 'logo', $logo);
        $tag = $this->tag_service->findOne($data['id']);

        if ($pack->isOk()) {
            return $this->gotoRoute("admin-ui-tag-show", [
                'id' => $data['id']
            ]);
        }

        return $this->page('tag/upload_logo', [
            'tag' => $tag,
            'errors' => $pack->getErrors()
        ]);
    }
}
