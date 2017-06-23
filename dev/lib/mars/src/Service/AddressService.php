<?php
namespace Mars\Service;

class AddressService extends MarsServiceBase
{
    public function fetchAreas($query = [])
    {
        return $this->repo('address')->fetchAreas($query);
    }

    public function findAddressById($id)
    {
        return $this->repo('address')->findAddressById($id);
    }
    public function createAddress($data = [])
    {
        return $this->repo('address')->createAddress($data);
    }

    public function updateAddress($data = [])
    {
        return $this->repo('address')->updateAddress($data);
    }
}
