<?php

namespace Ipar\Nos;

use NOS\NosClient;
use NOS\Core\NosException;

class Nos
{
    public $access_id;
    public $access_key;
    public $endpoint;
    public $bucket;
    public $base_path;
    protected $extMap = [
      'image/gif' => 'gif',
      'image/bmp' => 'bmp',
      'image/jpeg' => 'jpeg',
      'image/png' => 'png'
    ];

    public function __construct($access_id = '', $access_key = '', $endpoint = '', $bucket = '', $base_path = '/upload/img')
    {
        $this->access_id = $access_id;
        $this->access_key = $access_key;
        $this->endpoint = $endpoint;
        $this->bucket = $bucket;
        $this->base_path = trim($base_path, '/');
    }

    public function getNosClient()
    {
        return new NosClient($this->access_id, $this->access_key, $this->endpoint);
    }

    public function guessExt($file)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file->getPathname());
        return $this->extMap[$mimeType] ? $this->extMap[$mimeType] : '';
    }

    public function upload($file)
    {
        $tmp_file_path = $file->getRealPath();
        $dir = $this->base_path . date('/Y/m/d');
        $name = uniqid();
        $ext = $this->guessExt($file);
        $fileObj = $file->openFile('r');
        $fileContent = $fileObj->fread($fileObj->getSize());
        $this->getNosClient()->putObject($this->bucket, "$dir/$name.$ext", $fileContent);
        return [
            'dir' => '/' . $dir,
            'name' => $name,
            'ext' => $ext
        ];
    }
}
