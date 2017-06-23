<?php
namespace Gap\File;

class ImageDto
{
    public $dir;
    public $name;
    public $ext;
    public $full_dir;
    public $path;

    public function __construct($data)
    {
        $this->dir = prop($data, 'dir', '');
        $this->name = prop($data, 'name', '');
        $this->ext = prop($data, 'ext',  '');
        $this->full_dir = prop($data, 'full_dir', '');
        $this->path = prop($data, 'path', '');
    }

    public function resize($key, $dst, $src = [])
    {
        $img_opts = $this->toArray();
        image_tool()->resize($key, $img_opts, $dst, $src);
    }

    public function toArray()
    {
        return (array) $this;
    }
}
