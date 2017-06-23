<?php
namespace Gap\Database;

class Model implements \ArrayAccess, \IteratorAggregate, \Countable {


    public function __construct($data = [], $isForce = false) {
        $this->setData($data, $isForce);
        if (property_exists($this, 'path')) {
            if (!$this->path) {
                $this->path = uniqid() . time();
            }
        }
        if (!$this->created) {
            $this->created = date('Y-m-d H:i:s');
        }
    }

    public function setData($data = [], $isForce = false) {
        if ($isForce) {
            foreach ($data as $key => $val) {
                if (is_string($key)) {
                    $this->$key = $val;
                }
            }
        } else {
            foreach ($data as $key => $val) {
                if (property_exists($this, $key)) {
                    $this->$key = $val;
                }
            }
        }
        return $this;
    }

    public function getData() {
        return ((array) $this);
    }



    public function set($name, $val) {
        if (property_exists($this, $name)) {
            $this->$name = $val;
        }
        return $this;
    }

    public function get($key, $default = null) {
        return isset($this->$key) ? $this->$key : $default;
    }

    public function has($key) {
        return property_exists($this, $key);
    }

    public function remove($key) {
        unset($this->$key);
    }


    public function getImages() {
        if ($this->images_json) {
            return json_decode($this->images_json, true);
        } else {
            return [];
        }
    }

    public function setImages($images = []) {
        $this->images_json = json_encode($images);
    }

    public function __set($name, $val) {
        $this->set($name, $val);
    }
    public function __get($name) {
        return $this->get($name);
    }

    // implements ArrayAccess
    public function offsetSet($offset, $val) {
        $this->set($offset, $val);
    }
    public function offsetGet($offset) {
        return $this->get($offset);
    }
    public function offsetUnset($offset) {
        $this->remove($offset);
    }
    public function offsetExists($offset) {
        return $this->has($offset);
    }

    // implements IteratorAggregate
    public function getIterator() {
        return new \ArrayIterator((array) $this);
    }

    // implements Countable
    public function count() {
        return count((array)$this);
    }
}
