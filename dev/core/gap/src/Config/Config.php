<?php
namespace Gap\Config;

use Gap\Tool\FileCompilerTrait;

class Config implements \ArrayAccess {

    use FileCompilerTrait;

    protected $items = [];
    protected $includes = [];

    public function __construct($items = [])
    {
        if ($items) {
            $this->load($items);
        }
    }

    protected function itemSet($key, $value) {
        if (is_null($key)) return false;

        $array = &$this->items;
        $keys = explode('.', $key);
        while(isset($keys[1])) {
            $key = array_shift($keys);
            if ( ! isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;

        return true;
    }

    protected function value($value) {
        $rs = $value instanceof \Closure ? $value() : $value;
        if (is_array($rs)) {
            return new Config($rs);
        } else {
            return $rs;
        }
    }

    public function has($key) {

        if (empty($array) || is_null($key)) return false;

        $array = $this->items;
        if (array_key_exists($key, $array)) return true;

        foreach (explode('.', $key) as $segment) {
            if ( ! is_array($array) || ! array_key_exists($segment, $array)) {
                return false;
            }
            $array = $array[$segment];
        }

        return true;
    }

    public function get($key, $default = '') {
        if (is_null($key)) return null;

        $array = $this->items;
        if (isset($array[$key])) return $this->value($array[$key]);

        foreach (explode('.', $key) as $segment) {
            if ( ! is_array($array) || ! array_key_exists($segment, $array)) {
                return $this->value($default);
            }

            $array = $array[$segment];
        }

        return $this->value($array);
    }

    public function set($key, $value = null) {
        if (is_array($key)) {
            foreach ($key as $innerKey => $innerValue) {
                $this->set($innerKey, $innerValue);
            }
        } else if (is_string($key)) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $this->set($key . '.' . $subKey, $subValue);
                }
            } else {
                $this->itemSet($key, $value);
            }
        } else {
            throw new \RuntimeException('config error format');
        }
        return $this;
    }

    public function prepend($key, $value) {
        $array = $this->get($key);
        array_unshift($array, $value);
        $this->set($key, $array);
    }

    public function append($key, $value) {
        $this->push($key, $value);
    }

    public function push($key, $value) {
        $array = $this->get($key);
        $array[] = $value;
        $this->set($key, $array);
    }

    public function all() {
        return $this->items;
    }

    public function offsetExists($offset) {
        return $this->has($offset);
    }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset) {
        $this->set($key, null);
    }

    public function compile($target_path)
    {
        file_put_contents(
            $target_path,
            "<?php return " . var_export($this->items, true) . ";"
        );
    }

    public function clear()
    {
        $this->items = [];
    }

    public function load($items)
    {
        $this->items = $items;
    }
}
