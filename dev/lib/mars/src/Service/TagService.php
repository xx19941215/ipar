<?php
namespace Mars\Service;

class TagService extends \Gap\Service\ServiceBase
{
//    use TagMainServiceTrait;

//    protected $validator;
    protected $tag_repo;

    public function bootstrap()
    {
        $this->tag_repo = gap_repo_manager()->make('tag');
    }

    public function update($query,$data)
    {
        return $this->tag_repo->update($query, $data);
    }
    public function findOne($tag_id)
    {
        return $this->tag_repo->findOne(['id' => $tag_id]);
    }

    public function create($data = [])
    {
        return $this->tag_repo->create($data);
    }

    public function saveTag($title, $locale_id = 0)
    {
        $title = trim($title);
        $locale_id = (int) $locale_id;


        if (true !== ($validated = $this->valid()->validateTitle($title))) {
            return $validated;
        }

        return $this->repo('tag')->saveTag($title, $locale_id);
    }

    public function getTagByZcode($zcode)
    {
        return $this->tag_repo->getTagByZcode($zcode);
    }

    public function activateTagById($tag_id)
    {
        $tag_id = (int) $tag_id;
        if (0 >= $tag_id) {
            return $this->packError('tag_id', 'not-positive');
        }
        return $this->tag_repo->updateField(['id'=>$tag_id], 'status', 1);
    }

    public function deactivateTagById($tag_id)
    {
        $tag_id = (int) $tag_id;
        if (0 >= $tag_id) {
            return $this->packError('tag_id', 'not-positive');
        }
        return $this->tag_repo->updateField(['id'=>$tag_id], 'status', 0);
    }

    public function deleteTag($query)
    {
        $pack = $this->repo('tag')->deleteTag($query);
        if ($pack->isOk()) {
            $this->deleteCachedTag($pack->getItem('tag_id'));
        }
        return $pack;
    }

    public function deleteTagById($tag_id)
    {
        $tag_id = (int) $tag_id;
        return $this->deleteTag(['tag_id' => $tag_id]);
    }

    public function search($query = [])
    {
        return $this->tag_repo->search($query);
    }

    public function updateField($query, $field, $value)
    {
        return $this->tag_repo->updateField($query, $field, $value);
    }



    /*
    public function schTagDstSet($query = [])
    {
        return $this->dataSet(
            $this->tag_repo->schTagDstSsb($query)
        );
    }
     */








/* old code..
 *public function fetchTagTitle($tag_id, $locale_id)
    {
        $tag_main = $this->fetchTagMain($tag_id, $locale_id);
        return $tag_main->title;
    }

    public function hasVoted($tag_dst_id)
    {
        if (0 > ($tag_dst_id = (int) $tag_dst_id)) {
            return $this->packError('tag_dst_id', 'not-positive');
        }

        return $this->tag_repo->hasVoted($tag_dst_id);
    }

    protected function fetchCachedTagMain($tag_id, $locale_id)
    {
        if ($str = $this->cache()->hGet("t-$tag_id", "$locale_id")) {
            $tag_main = dto_decode($str, 'tag_main');
            return $tag_main;
        }

        return null;
    }

    protected function setCachedTagMain($tag_id, $locale_id, $tag_main)
    {
        $this->cache()->hSet("t-$tag_id", "$locale_id", json_encode($tag_main));
    }

    protected function deleteCachedTag($tag_id)
    {
        $this->cache()->delete("t-$tag_id");
    }*/

    protected function valid()
    {
        if ($this->validator) {
            return $this->validator;
        }

        $this->validator = new \Mars\Validator\TagValidator();
        return $this->validator;
    }
}
