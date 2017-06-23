<?php
namespace Ipar\Rest;

class AddressController extends \Gap\Routing\Controller
{
    public function fetchAreas()
    {
        $parent_id = $this->request->request->get('parent_id');
        $title = $this->request->request->get('title');
        $address = service('address');
        $data = $address->fetchAreas([
            'title' => $title,
            'parent_id' => $parent_id
            ]);
        return $data;
    }
}
