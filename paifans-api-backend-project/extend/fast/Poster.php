<?php

namespace fast;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;

class Poster
{

    private $imgFolder;
    private $fontFolder;

    function __construct($imgFolder='./assets',  $fontFolder='./assets/fonts') {
        $this->imgFolder = $imgFolder;
        $this->fontFolder = $fontFolder;
    }

    public function create($url, $userName, $avatar='')
    {
        // 1 获取背景图尺寸
        list($bg_w,$bg_h) = getimagesize($this->imgFolder."/bg.png");
        // 2 创建画图
        $img = @imagecreatetruecolor($bg_w,$bg_h);
        // 3 填充画布背景颜色
        $img_bg_color = imagecolorallocate($img,255,255,255);
        imagefill($img,0,0,$img_bg_color);
        // 4 将背景图填充到画布
        $bg_img = $this->getImgReource($this->imgFolder."/bg.png");
        imagecopyresized($img,$bg_img,0,0,0,0,$bg_w,$bg_h,$bg_w,$bg_h);
        // 5 生成二维码， 填充用户二维码
        $qecodeName = $this->generateQrcode($url);;
        $qrcode = $this->getImgReource($qecodeName);
        // 获取二维码尺寸
        list($qr_w,$qr_h) = getimagesize($qecodeName);
        imagecopyresized($img,$qrcode,221,1175,0,0,310,300,$qr_w,$qr_h);
        // // 6 填充用户图像
        // $head_img = $this->imgFolder."/none.jpeg";
        // if ($avatar) {
        //     $head_img = '.'.$avatar;
        // }
        // $user_img = $this->getImgReource($head_img);
        // list($user_w,$user_h) = getimagesize($this->imgFolder."/none.jpeg");
        // imagecopyresized($img,$user_img,320,370,0,0,130,130,$user_w,$user_h);
        // // 填空用户名
        // $font_color = ImageColorAllocate($img,0,0,0); //字体颜色
        // $font_ttf =  "./assets/fonts/verdana.ttf";
        // imagettftext($img,32,0,240,630,$font_color,realpath($font_ttf),$userName);
        // 8 保存图片
        $posterName = "./poster/".date("YmdHis", time()).$this->generateRandomString().".png";
        imagepng($img,$posterName);

        return $posterName;

    }

    private function generateQrcode($url)
    {
        $qrCode = new QrCode($url);
        $qrCode->setLogoPath('.'.config('site.app_logo'));
        $qrCode->setLogoWidth(100);
        $qrCode->setLogoHeight(100);
        $qrCode->setSize(600);
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
//        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);
        $qecodeName = "./qrcode/".date("YmdHis", time()).$this->generateRandomString().".png";  // 2017-12-14 23:13:51
        $qrCode->writeFile($qecodeName);
        return $qecodeName;
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /**
     * 获取图像文件资源
     * @param string $file
     * @return resource
     */
    protected function getImgReource($file){
        $file_ext = pathinfo($file,PATHINFO_EXTENSION);
        switch ($file_ext){
            case 'jpg':
            case 'jpeg':
                $img_reources = @imagecreatefromjpeg($file);
                break;
            case 'png':
                $img_reources = @imagecreatefrompng($file);
                break;
            case 'gif':
                $img_reources = @imagecreatefromgif($file);
                break;
        }
        return  $img_reources;
    }

}