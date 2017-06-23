<?php
namespace Gap\Pack;

trait PackTrait
{

    public function packItem($key, $val)
    {
        return new PackDto(1, [$key => $val]);
    }

    public function packItems($items)
    {
        return new PackDto(1, $items);
    }

    public function packError($key, $msg)
    {
        return new PackDto(0, [$key => $msg]);
    }

    public function packErrors($errors)
    {
        return new PackDto(0, $errors);
    }

    public function packOk()
    {
        return new PackDto(1);
    }

    public function packNotFound($key)
    {
        return new PackDto(0, [$key => 'not-found']);
    }

    // deprecated
    public function packNotEmpty($key)
    {
        return new PackDto(0, [$key => 'empty']);
    }

    public function packEmpty($key)
    {
        return new PackDto(0, [$key => 'empty']);
    }

    public function packNotEmail($key)
    {
        return new PackDto(0, [$key => 'not-email']);
    }

    // deprecated
    public function packOutLength($key, $min, $max)
    {
        return new PackDto(0, [$key => ['out-range-%d-and-%d', $min, $max]]);
    }

    public function packOutRange($key, $min, $max)
    {
        return new PackDto(0, [$key => ['out-range-%d-and-%d', $min, $max]]);
    }

    public function packExists($key)
    {
        return new PackDto(0, [$key => 'exists']);
    }
}
