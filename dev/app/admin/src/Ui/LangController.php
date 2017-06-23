<?php
namespace Admin\Ui;

class LangController extends AdminControllerBase {
    public function index()
    {
        $query = $this->request->query;

        $search = $query->get('s');
        $locale = $query->get('locale');

        $rows = lang()->find($search, $locale);
        return $this->page('lang/index', [
            'search' => $search,
            'locale' => $locale,
            'rows' => $rows
        ]);
    }
    public function savePost()
    {
        $post = $this->request->request;
        lang()->set(
            $post->get('locale'),
            $post->get('str'),
            $post->get('trans')
        );
        return $this->gotoRoute('admin-lang');
    }
}
