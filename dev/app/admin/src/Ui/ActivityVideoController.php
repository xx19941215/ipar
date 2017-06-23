<?php

namespace Admin\Ui;

class ActivityVideoController extends AdminControllerBase
{
    public function configVideo()
    {
        $aid = $this->request->get('id');
        $video = $this->service('activity_video')->getVideoByAid($aid);
        return $this->page('activity/config_video', [
            'video' => $video
        ]);
    }

    public function postConfigVideo()
    {
        $video = $this->request->request->get('video');
        $aid = $this->request->request->get('id');
        $this->service('activity_video')->saveVideo($aid, $video);

        $referer = $this->request->headers->get('referer');
        return $this->gotoUrl($referer);
    }
}
