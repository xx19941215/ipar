<?php
namespace Ipar\Repo;

class ActivityVideoRepo extends IparRepoBase
{
    public function saveVideo($aid, $video)
    {
        $this->db->update('activity_list')
            ->set('video', $video)
            ->where('id', '=', $aid)
            ->execute();
    }

    public function getVideoByAid($aid)
    {
        return $this->db->select('video')
            ->from('activity_list')
            ->where('id', '=', $aid)
            ->fetchOne()->video;
    }
}
