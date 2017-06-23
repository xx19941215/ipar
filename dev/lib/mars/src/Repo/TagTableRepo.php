<?php
namespace Mars\Repo;

class TagTableRepo extends \Gap\Repo\TableRepoBase
{
    protected $table_name = 'tag';
    protected $dto = 'tag';
    protected $fields = [
        'id' => 'int',
        'zcode' => 'str',
        'parent_id' => 'int',
        'locale_id' => 'int',
        'title' => 'str',
        'content' => 'str',
        'child_count' => 'int',
        'dst_count' => 'int',
        'vote_total_count' => 'int',
        'status' => 'int',
        'logo' => 'str',
        'uid' => 'int'
    ];


    public function search($query = [])
    {
        $ssb = $this->db->select()
            ->from($this->table_name)
            ->setDto($this->dto);

        if ($search = prop($query, 'search', '')) {
            $ssb->where('title', 'LIKE', "%$search%");
        }
        $ssb->orderBy('changed', 'desc');
        return $this->dataSet($ssb);
    }

    public function getTagByZcode($zcode)
    {
        return $this->db->select()
            ->from('tag')
            ->where('zcode', '=', $zcode)
            ->setDto('tag')
            ->fetchOne();
    }

    protected function validate($data)
    {
        $title = prop($data, 'title');
        if (!is_string($title) || empty($title)) {
            $this->addError('title', 'empty');
        }

        if ($existed = $this->findOne(['title' => $title], ['id'])) {
            if ($existed->id != prop($data, 'id')) {
                $this->addError('title', 'already-exists');
            }
        }
    }
}
