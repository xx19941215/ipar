<?php
/**
 * PHP Version 5.6.
 *
 * @category Rest
 *
 * @author Zjh <zhanjh@126.com>
 * @license http:://www.tecposter.cn/bsd-licence BSD Licence
 *
 * @link http:://www.tecposter.cn/
 **/

namespace Ipar\Rest;

use Gap\Routing\Controller as RoutingController;
use Gap\File\ImageDto as ImageDto;

class ImgController extends RoutingController
{

    public function uploadFigurePost()
    {

        if (!$img_file = $this->request->files->get('img')) {
            return $this->badRequest();
        }
        $bucket = 'ipar-upload';

        $access_id = config()->get("nos.$bucket.NOS_ACCESS_ID");
        $access_key = config()->get("nos.$bucket.NOS_ACCESS_KEY");
        $endpoint = config()->get("nos.$bucket.NOS_ENDPOINT");
        $nos = new \Ipar\Nos\Nos($access_id, $access_key, $endpoint, $bucket);
        $res = $nos->upload($img_file);

        return $this->packItem('img', [
            'site' => config()->get('img.nos_ipar_upload'),
            'dir' => $res['dir'],
            'name' => $res['name'],
            'ext' => $res['ext']
        ]);
    }

    public function retrieveImagePost()
    {
        $image_url = $this->request->request->get('imageUrl');

        $curl = curl_init();
        $img_tool = image_tool();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $image_url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_BINARYTRANSFER => 1,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0'
        ));

        $img_file = curl_exec($curl);

        $img_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

        curl_close($curl);

        $exts = array(
            'image/jpeg' => 'jpeg',
            'image/gif' => 'gif',
            'image/png' => 'png'
        );

        $image_ext = $exts[$img_type];
        $image_name = uniqid();


        $relative_dir = date('/Y/m/d');

        $site = config()->get('img.site');
        $site_dir = config()->get("site.$site.dir");
        $base_dir = config()->get('img.base_dir');

        $full_dir = $site_dir . $base_dir . $relative_dir;
        $image_path = $full_dir . '/' . $image_name . '.' . $image_ext;

        if (file_exists($image_path)) unlink($image_path);
        if (!is_dir($full_dir)) mkdir($full_dir, 0777, true);

        $fp = fopen($image_path, 'x');
        fwrite($fp, $img_file);
        fclose($fp);

        $image = new ImageDto([
            'dir' => $base_dir.$relative_dir,
            'name' => $image_name,
            'ext' => $image_ext,
            'full_dir' => $full_dir,
            'path' => "$full_dir/$image_name.$image_ext",
        ]);

        $image->resize('small', ['w' => 200]);
        $image->resize('large', ['w' => 600]);
        $image->resize('cover', ['w' => 235, 'h' => 175]);

        $image_local_url = config()->get('site.static.host') . $image->dir . '/' . $image->name . '.' .$image->ext;

        return $this->packItem('img', [
            'site' => $site,
            'dir' => $image->dir,
            'name' => $image->name,
            'ext' => $image->ext
        ]);

    }
}
