<?php
namespace Admin\Ui;

class TransController extends AdminControllerBase
{
    private $translator;

    public function index()
    {
        $page = $this->request->query->get('page', 1);
        $query = $this->request->query->get('query', '');

        $user = $this->getCurrentUser();

        $translator = $this->getTranslator();
        $translator->setCurrentPage($page);
        $locale = '';
        //$locale = $this->request->query->get('locale', '');

        $transes = $translator->getTranses($query);
        foreach ($transes as $trans) {
            $trans->transes = $translator->getAllTranses($trans->str);
        }

        return $this->page('trans/index', [
            'transes' => $transes,
            'user' => $user,
            'page' => $page,
            'page_count' => $translator->getPageCount($query, $locale),
        ]);
    }

    public function search()
    {
        return $this->page('trans/search');
    }

    public function show()
    {
        return $this->page('trans/show');
    }

    public function add()
    {
        return $this->page('trans/add');
    }

    public function addPost()
    {
        $post = $this->request->request;
        $locale = $this->request->request->get('locale');
        $str = $this->request->request->get('str');
        $trans = $this->request->request->get('trans');
        $translator = $this->getTranslator();
        $pack = $translator->set($locale, $str, $trans);
        //if ($pack->isOk()) {
        return $this->gotoRoute('admin-trans');
      /*  } else {
            $data = $post->all();
            $data['errors'] = $pack->getErrors();
            return $this->page('trans/add', $data);
        }*/
    }

    public function edit()
    {
        $trans = $this->getTransFromParam();
        $transes = [];
        $translator = $this->getTranslator();

        $transes = $translator->getAllTranses($trans->str);
        return $this->page('trans/edit', [
            'str' => $trans->str,
            'transes' => $transes
        ]);
    }

    public function editPost()
    {
        $str = $this->request->request->get('str');
        $transes = $this->request->request->get('transes');

        $translator = $this->getTranslator();
        foreach ($transes as $locale => $trans) {
            if ($trans) {
                $translator->set($locale, $str, $trans);
            }
        }

        return $this->gotoRoute('admin-trans');
    }

    protected function getTransFromParam()
    {
        if ($trans_id = $this->getParam('id')) {
            $translator = $this->getTranslator();
            return $translator->getTransByIdFromDb($trans_id);
        } else {
            return null;
        }
    }

    protected function getTranslator()
    {
        if ($this->translator) {
            return $this->translator;
        }

        $config = config();
        $this->translator = new \Gap\I18n\TranslatorAdmin(
            $config->get('db.i18n'),
            $config->get('cache.i18n'),
            locale_set(),
            $config->get('i18n.translator.db_table'),
            $config->get('i18n.locale.default')
        );
        return $this->translator;
    }
}
