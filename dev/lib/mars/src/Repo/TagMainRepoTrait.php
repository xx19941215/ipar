<?php
namespace Mars\Repo;

trait TagMainRepoTrait
{
    public function createTagMain($tag_id, $locale_id, $title, $content = '', $zcode = null)
    {
        if (!$zcode) {
            $zcode = $this->generateZcode();
        }

        if ($this->db->insert('tag_main')
            ->value('tag_id', $tag_id, 'int')
            ->value('title', $title)
            ->value('content', $content)
            ->value('locale_id', $locale_id)
            ->value('zcode', $zcode)
            ->execute()
        ) {
            return $this->packOk();
        }

        return $this->packError('tag_main', 'insert-failed');
    }

    public function updateTagMain($tag_id, $locale_id, $title, $content, $zcode)
    {
        if (!$zcode) {
            $zcode = $this->generateZcode();
        }
        if ($this->db->update('tag_main')
            ->set('title', $title)
            ->set('content', $content)
            ->set('zcode', $zcode)
            ->where('tag_id', '=', $tag_id, 'int')
            ->andWhere('locale_id', '=', $locale_id, 'int')
            ->execute()
        ) {
            return $this->packOk();
        }
        return $this->packError('tag_main', 'update-failed');
    }

    public function deleteTagMain($query = [])
    {
        $dsb = $this->db->delete('tag_main')
            ->from('tag_main');
        if ($tag_id = prop($query, 'tag_id')) {
            $dsb->andWhere('tag_id', '=', $tag_id, 'int');
        }

        if ($locale_id = prop($query, 'locale_id')) {
            $dsb->andWhere('locale_id', '=', $locale_id, 'int');
        }

        if (!$dsb->getWheres()) {
            return $this->packError('query', 'error-query');
        }

        if ($dsb->execute()) {
            return $this->packOk();
        }

        return $this->packError('tag_main', 'delete-failed');
    }

    public function schTagMainSsb($query = [])
    {
        $ssb = $this->db->select()
            ->from('tag_main')
            ->setDto('tag_main');

        if ($tag_id = prop($query, 'tag_id')) {
            $ssb->andWhere('tag_id', '=', $tag_id, 'int');
        }
        if ($locale_id = prop($query, 'locale_id')) {
            $ssb->andWhere('locale_id', '=', $locale_id, 'int');
        }
        if ($title = prop($query, 'title')) {
            $ssb->andWhere('title', '=', $title);
        }
        if ($zcode = prop($query, 'zcode')) {
            $ssb->andWhere('zcode', '=', $zcode);
        }
        if ($keywords = prop($query, 'keywords')) {
            $ssb->andWhere('title', 'LIKE', "%$keywords%");
        }
        $ssb->orderBy('locale_id');
        return $ssb;
    }

    public function fetchTagMain($tag_id, $locale_id = 0)
    {
        $ssb = $this->schTagMainSsb([
            'tag_id' => $tag_id,
            'locale_id' => $locale_id
        ]);
        if ($tag_main = $ssb->fetchOne()) {
            return $tag_main;
        }

        $ssb = $this->schTagMainSsb([
            'tag_id' => $tag_id
        ]);
        if ($tag_main = $ssb->fetchOne()) {
            return $tag_main;
        }

        return null;
    }

    public function findTagMain($query = [])
    {
        $ssb = $this->schTagMainSsb($query);
        if (!$ssb->getWheres()) {
            return null;
        }
        return $ssb->fetchOne();
    }

    public function schTagMainSet($query = [])
    {
        return $this->dataSet(
            $this->schTagMainSsb($query)
        );
    }

}
