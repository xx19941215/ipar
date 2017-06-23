<?php
namespace Mars\Service;

trait TagMainServiceTrait
{
    public function updateTagMain($tag_id, $locale_id, $title, $content, $zcode)
    {
        $tag_id = (int) $tag_id;
        $locale_id = (int) $locale_id;
        $title = trim($title);
        $content = trim($content);
        $zcode = trim($zcode);

        if (true !== ($validated = $this->validateTagMain($tag_id, $locale_id, $title, $zcode))) {
            return $validated;
        }

        $tag_repo = $this->repo('tag');

        if (!$tag_repo->findTagMain(['tag_id' => $tag_id, 'locale_id' => $locale_id])) {
            return $this->packError('tag_id-locale_id', 'not-found');
        }

        return $tag_repo->updateTagMain($tag_id, $locale_id, $title, $content, $zcode);
    }

    public function createTagMain($tag_id, $locale_id, $title, $content, $zcode = null)
    {
        $tag_id = (int) $tag_id;
        $locale_id = (int) $locale_id;
        $title = trim($title);
        $content = trim($content);


        if (true !== ($validated = $this->validateTagMain($tag_id, $locale_id, $title, $zcode))) {
            return $validated;
        }

        $tag_repo = $this->repo('tag');

        if ($tag_repo->findTagMain(['tag_id' => $tag_id, 'locale_id' => $locale_id])) {
            return $this->packError('tag_id-locale_id', 'duplicated');
        }

        return $tag_repo->createTagMain($tag_id, $locale_id, $title, $content, $zcode);
    }

    public function deleteTagMain($query = [])
    {
        return $this->repo('tag')->deleteTagMain($query);
    }

    public function schTagMainSet($query = [])
    {
        return $this->repo('tag')->schTagMainSet($query)
    }

    public function findTagMain($query = [])
    {
        return $this->repo('tag')->findTagMain($query);
    }

    protected function validateTagMain($tag_id, $locale_id, $title, $zcode)
    {
        if ($tag_id <= 0) {
            return $this->packError('tag_id', 'not-positive');
        }

        if ($locale_id <= 0) {
            return $this->packError('locale_id', 'not-positive');
        }

        if (true !== ($validated = $this->valid()->validateTitle($title))) {
            return $validated;
        }

        $tag_repo = $this->repo('tag');
        if (!$tag_repo->findTag(['tag_id' => $tag_id])) {
            return $this->packError('tag_id', 'not-found');
        }

        if ($tag_repo->findTagMain(['title' => $title])) {
            return $this->packError('title', 'duplicated');
        }

        if ($zcode) {
            if ($tag_repo->findTagMain(['zcode' => $zcode])) {
                return $this->packError('zcode', 'duplicated');
            }
        }

        return true;
    }
}
