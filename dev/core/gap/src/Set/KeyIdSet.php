<?php
namespace Gap\Set;

class KeyIdSet
{
    protected $set = [];
    protected $flipped = [];

    public function __construct($type_set)
    {
        $this->loadSet($type_set);
    }

    public function loadSet($set)
    {
        foreach ($set as $key => $val) {
            if (is_array($val)) {
                $this->loadSet($val);
                continue;
            }

            if ($val = (int) $val) {
                $this->set[$key] = $val;
            }

        }
    }


    public function getSet()
    {
        return $this->set;
    }

    public function getFlipped()
    {
        if ($this->flipped) {
            return $this->flipped;
        }

        $this->flipped = array_flip($this->set);
        return $this->flipped;
    }

    public function getId($key)
    {
        return (int) prop($this->set, $key, 0);
    }

    public function getKey($id)
    {
        return prop($this->getFlipped(), (int) $id);
    }
}
