<?php

namespace Admin\Ui;

class EventController extends AdminControllerBase
{
    public function index()
    {
        $page = $this->request->query->get('page', '1');
        $events = $this->service('event')->getEvents();
        $events->setCurrentPage($page);

        $user = $this->getCurrentUser();

        return $this->page('event/index', [
            'events' => $events,
            'user' => $user,
        ]);
    }

    public function show()
    {
        $event = $this->getEventFromParam();
        return $this->page('event/show', [
            'event' => $event,
        ]);
    }

    protected function getEventFromParam()
    {
        $id = $this->getParam('id');
        return $this->service('event')->getEventById($id);
    }
}
