<?php
namespace Ipar\Repo;

class ArticleRepo extends IparRepoBase
{

    public function search($query = [])
    {
        $ssb = $this->db->select()
            ->where(['e', 'title'], 'LIKE', "%{$query}%")
            ->orWhere(['e', 'content'], 'LIKE', "%{$query}%");

        return $this->dataSet($ssb);
    }

    public function getArticleById($id)
    {
        return $this->db->select()
            ->from('article')
            ->where('id', '=', $id)
            ->setDto('article')
            ->fetchOne();
    }

    public function getArticleByZcode($zcode)
    {
        return $this->db->select()
            ->from('article')
            ->where('zcode', '=', $zcode)
            ->setDto('article')
            ->fetchOne();
    }

    public function getArticleBd($locale_id = '')
    {
        $bd = $this->db->select()
            ->from('article')
            ->orderBy('created', 'DESC')
            ->where('status', '=', 1, 'int');

        if ($locale_id) {
            $bd->andWhere('locale_id', '=', $locale_id);
        }
        $bd->setDto('article')
            ->fetchAll();

        return $bd;
    }

    public function getArticleBdAll()
    {

        $bd = $this->db->select()
            ->from('article')
            ->orderBy('changed', 'DESC')
            ->setDto('article');

        $bd->fetchAll();

        return $bd;
    }

    protected function prepareBd()
    {
        return $this->db->select(
            ['a', 'id'],
            ['a', 'uid'],
            ['a', 'zcode'],
            ['a', 'status'],
            ['a', 'created'],
            ['a', 'changed'],
            ['a', 'title'],
            ['a', 'content'],
            ['a', 'locale_id'],
            ['a', 'original_id']
        )
            ->from(['article', 'a']);
    }

    public function schArticleBd($opts = [])
    {
        $query = prop($opts, 'query', '');

        $builder = $this->prepareBd();

        if ($query) {
            $builder->startGroup()
                ->where(['a', 'title'], 'LIKE', "%{$query}%")
                ->orWhere(['a', 'content'], 'LIKE', "%{$query}%")
                ->endGroup();
        }

        $order = prop($opts, 'order', 'default');

        if ($order == 'default') {
            $builder->orderBy(['a', 'id'], 'DESC');

        } else if ($order == 'created') {
            $builder->orderBy(['a', 'id'], 'DESC');

        } else {
            var_dump($order);
            _debug('unexpected entity order');
        }

        return $builder;
    }

    public function createArticle($title, $content, $locale_id, $original_id = '')
    {
        $title = trim($title);
        $title = preg_replace('!\s+!', ' ', $title);

        $this->db->beginTransaction();

        $uid = current_uid();

        $db = $this->db->insert('article')
            ->value('uid', $uid, 'int')
            ->value('title', $title)
            ->value('content', $content)
            ->value('locale_id', $locale_id)
            ->value('imgs', json_encode($this->extractImgs($content)))
            ->value('zcode', $this->generateZcode());

        if (!empty($original_id)) {
            $db->value('original_id', $original_id);
        }

        if (!$db->execute()) {
            $this->db->rollback();
            return $this->packError('article', 'insert-failed');
        }

        $article_id = $this->db->lastInsertId();

        // $this->createArticleTags($article_id, $tag_ids);

        if (!$original_id) {
            $opts = ["original_id" => $article_id];
            if (!$this->updateArticle($article_id, $opts)) {
                $this->db->rollback();
                return $this->packError('articleOriginalId', 'insert-failed');
            };

        }

        $this->db->commit();
        return $this->packOk();
    }

    public function createArticleTags($article_id, $tag_ids)
    {
        foreach ($tag_ids as $tag_id) {
            if (!$this->db->insert('article_tag')
                ->value('article_id', $article_id, 'int')
                ->value('tag_id', $tag_id, 'int')
                ->execute()
            ) {
                return $this->rollback('articleTags', 'insert-failed');
            }
        }
    }

    public function deleteArticle($article_id)
    {
        $this->db->beginTransaction();

        if (!$this->db->delete('article')->from('article')->where('id', '=', $article_id, 'int')->execute()) {
            $this->db->rollback();
            return $this->packError('article', 'delete-failed');
        }
        /*
        if (!$this->db->delete('article_tag')->from('article_tag')->where('article_id', '=', $article_id, 'int')->execute()) {
            $this->db->rollback();
            return $this->packError('article_tag', 'delete-failed');
        }
         */

        $this->db->commit();
        return $this->packOk();
    }

    public function deleteArticleSimple($article_id)
    {
        if (!$this->db->delete()->from('article')->where('id', '=', $article_id, 'int')->execute()) {
            return $this->packError('article', 'delete-failed');
        }

        return $this->packOk();
    }

    public function deleteArticleTags($article_id)
    {
        try {
            $this->db->delete('article_tag')->where('article_id', '=', $article_id);
        } catch (\Exception $e) {
            return $this->rollback(['execption' => $e->getMessage()]);
        }
    }

    public function updateArticle($article_id, $opts)
    {
        if(!empty($opts['content'])){
            $opts['imgs'] = json_encode($this->extractImgs($opts['content']));
        }
        $bd = $this->db->update('article')
            ->where('id', '=', $article_id, 'int');

        foreach ($opts as $key => $value) {
            $bd->set($key, $value);
        };
        if ($bd->execute()) {
            return $this->packOk();
        } else {
            return $this->packError('article', 'update-failed');
        }
    }

    public function getArticleSet($locale_id)
    {
        return $this->dataSet(
            $this->getArticleBd($locale_id)
        );
    }

    public function getArticleSetAll()
    {
        return $this->dataSet(
            $this->getArticleBdAll()
        );
    }

    public function schArticleSet($opts = [])
    {
        return $this->dataSet(
            $this->schArticleBd($opts)
        );
    }

    public function contentExtractImgs()
    {
        $ssb = $this->db
            ->select('id', 'content')
            ->from('article');

        $article_set = $this->dataSet($ssb);
        $article_set->setCountPerPage(0);

        foreach ($article_set->getItems() as $article) {
            $imgs = $this->extractImgs($article->content);

            $updated = $this->db->update('article')
                ->where('id', '=', $article->id, 'int')
                ->set('imgs', json_encode($imgs))
                ->execute();

            if (!$updated) {
                echo "{$article->id} failed \t";
            }
        }

    }

    protected function extractImgs($content)
    {
        if (!$content) {
            return false;
        }

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($content);
        $doc->preserveWhiteSpace = false;
        $imgs = [];
        if ($img_elems = $doc->getElementsByTagName('img')) {
            foreach ($img_elems as $elem) {
                if ($data_dir = $elem->getAttribute('data-dir')) {
                    $img = [
                        'dir' => $data_dir,
                        'name' => $elem->getAttribute('data-name'),
                        'ext' => $elem->getAttribute('data-ext'),
                    ];
                    if ($protocol = $elem->getAttribute('data-protocol')) {
                        $img['protocol'] = $protocol;
                    }
                    if ($site = $elem->getAttribute('data-site')) {
                        $img['site'] = $site;
                    }
                    $imgs[] = $img;
                }

            }
        }
        return $imgs;
    }
}

?>
