<?php

namespace Gap\File;

class ImageTool
{
    use \Gap\Pack\PackTrait;

    protected $site_dir;
    protected $base_dir;

    public function __construct($site_dir, $base_dir)
    {
        $this->site_dir = $site_dir;
        $this->base_dir = $base_dir;
    }

    public function save($file)
    {
        $relative_dir = date('/Y/m/d');
        $full_dir = $this->site_dir . $this->base_dir . $relative_dir;

        if (!is_dir($full_dir)) {
            mkdir($full_dir, 0777, true);
        }


        if (function_exists('isValid') && !$file->isValid()) {
            return $this->packError('error', $file->getError());
        }

        $ext = strtolower($file->guessExtension());

        $name = uniqid();
        $file->move($full_dir, "$name.$ext");

        return $this->packItem(
            'image',
            new ImageDto([
               'dir' => $this->base_dir.$relative_dir,
               'name' => $name,
               'ext' => $ext,
               'full_dir' => $full_dir,
               'path' => "$full_dir/$name.$ext",
            ])
        );
    }

    public function edit($url)
    {
        $relative_dir = date('/Y/m/d');
        $full_dir = $this->site_dir . $this->base_dir . $relative_dir;

        if (!is_dir($full_dir)) {
            mkdir($full_dir, 0777, true);
        }

        $arr = explode('.', $url);
        $length = count($arr);

        $ext = $arr[$length - 1];

        $name = uniqid();
        $ext = strtolower($ext);
        copy($url, "$full_dir/$name.$ext");

        return $this->packItem(
            'image',
            new ImageDto([
                'dir' => $this->base_dir.$relative_dir,
                'name' => $name,
                'ext' => $ext,
                'full_dir' => $full_dir,
                'path' => "$full_dir/$name.$ext",
            ])
        );
    }

    public function resize($key, $img_opts, $dst, $src = [])
    {
        $dst_x = isset($dst['x']) ? $dst['x'] : 0;
        $dst_y = isset($dst['y']) ? $dst['y'] : 0;
        $dst_w = isset($dst['w']) ? $dst['w'] : 0;
        $dst_h = isset($dst['h']) ? $dst['h'] : 0;

        $src_x = 0;
        $src_y = 0;
        $src_w = 0;
        $src_h = 0;

        if ($src) {
            $src_x = isset($src['x']) ? $src['x'] : 0;
            $src_y = isset($src['y']) ? $src['y'] : 0;
            $src_w = isset($src['w']) ? $src['w'] : 0;
            $src_h = isset($src['h']) ? $src['h'] : 0;
        } else {
            $o_size = getimagesize($img_opts['path']);
            $o_w = $o_size[0];
            $o_h = $o_size[1];

            if ($dst_w && $dst_h) {
                if ($o_w * $dst_h > $o_h * $dst_w) {
                    $src_h = $o_h;
                    $src_w = $o_h * $dst_w / $dst_h;
                    $src_y = 0;
                    $src_x = ($o_w - $src_w) / 2;
                } else {
                    $src_w = $o_w;
                    $src_h = $o_w * $dst_h / $dst_w;
                    $src_x = 0;
                    $src_y = ($o_h - $src_h) / 2;
                }
            } elseif ($dst_w) {
                $src_w = $o_w;
                $src_h = $o_h;
                $dst_h = $dst_w * $o_h / $o_w;
            } elseif ($dst_h) {
                $src_w = $o_w;
                $src_h = $o_h;
                $dst_w = $dst_h * $o_w / $o_h;
            }
        }
        $ext = $img_opts['ext'];
        $origin_img = $this->createImg($img_opts['path'], $ext);
        if(isset($src['rotate'])) {
          $origin_img = imagerotate($origin_img, $src['rotate'], 0);
        }
        $new_img = $this->resizeImg(
            $ext,
            $origin_img,
            // x, y
            $dst_x,
            $dst_y,
            $src_x,
            $src_y,
            // w, h
            $dst_w,
            $dst_h,
            $src_w,
            $src_h
        );
        $target = $img_opts['full_dir'].'/'.$img_opts['name']."-$key".'.'.$ext;

        return $this->saveImg($ext, $new_img, $target);
    }

    protected function createImg($img_path, $ext)
    {
        if ($ext === 'jpg' || $ext === 'jpeg') {
            return imagecreatefromjpeg($img_path);
        }
        if ($ext === 'gif') {
            return imagecreatefromgif($img_path);
        }
        if ($ext === 'png') {
            return imagecreatefrompng($img_path);
        }

        return false;
    }

    protected function resizeImg(
        $ext,
        $src,
        // x, y
        $dst_x,
        $dst_y,
        $src_x,
        $src_y,
        // w, h
        $dst_w,
        $dst_h,
        $src_w,
        $src_h
    ) {
        $dst = imagecreatetruecolor($dst_w, $dst_h);

        if ($ext === 'gif') {
            $background = imagecolorallocate($dst, 0, 0, 0);
            imagecolortransparent($dst, $background);
        } elseif ($ext === 'png') {
            $background = imagecolorallocate($dst, 0, 0, 0);
            imagecolortransparent($dst, $background);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }

        imagecopyresampled(
            $dst,
            $src,
            $dst_x,
            $dst_y,
            $src_x,
            $src_y,
            $dst_w,
            $dst_h,
            $src_w,
            $src_h
        );

        return $dst;
    }

    protected function saveImg($ext, $img, $target)
    {
        if ($ext === 'jpg' || $ext === 'jpeg') {
            return imagejpeg($img, $target, 77);
        }
        if ($ext === 'gif') {
            return imagegif($img, $target);
        }
        if ($ext === 'png') {
            return imagepng($img, $target, 7);
        }

        return false;
    }
}
