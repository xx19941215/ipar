<?php
namespace Gap\Pack;

class PackDto
{
    public $ok;
    public $items;
    public $errors;

    public function __construct($ok, $arr = null)
    {
        $this->ok = $ok;
        if ($arr) {
            if ($ok === 1) {
                $this->items = $arr;
                $this->errors = [];
                return;
            }

            $this->errors = $arr;
            $this->items = [];
            return;
        }
    }

    public function isOk()
    {
        return ($this->ok === 1);
    }

    public function addItem($key, $item)
    {
        $this->items[$key] = $item;
        return $this;
    }

    public function addError($key, $error)
    {
        $this->errors[$key] = $error;
        return $this;
    }

    public function getItem($key)
    {
        return isset($this->items[$key]) ? $this->items[$key] : '';
    }

    public function getError($key)
    {
        return isset($this->errors[$key]) ? $this->errors[$key] : '';
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function toArray()
    {
        if ($this->isOk()) {
            return [
                'ok' => 1,
                'items' => $this->items
            ];
        }

        return [
            'ok' => 0,
            'errors' => $this->errors
        ];
    }
}
