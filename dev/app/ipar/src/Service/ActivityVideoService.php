<?php

namespace Ipar\Service;

class ActivityVideoService extends IparServiceBase
{
    public function saveVideo($aid, $video)
    {
            return $this->repo('activity_video')->saveVideo($aid, $video);
    }

    public function getVideoByAid($aid)
    {
            return $this->repo('activity_video')->getVideoByAid($aid);
    }
}
