<?php

namespace fast;

use think\Request;

class Sms
{
    protected $CorpID;
    protected $Pwd;

    public function __construct()
    {
        $this->CorpID = 'HFBX011887';
        $this->Pwd = '20210820@';
    }

    //发送短信
    public function send($Mobile,$Cell)
    {
        $SendTime = "";
        $Content = '您的验证码为: '.$Cell.'【派粉】';
        header("Content-type: text/html; charset=utf-8");
        date_default_timezone_set('PRC'); //设置默认时区为北京时间
        $url = "https://sdk2.028lk.com/sdk2/BatchSend2.aspx?";
        $Contents = rawurlencode(mb_convert_encoding($Content, "gb2312", "utf-8")); //短信内容做GB2312转码处理
        $curpost = "CorpID=".$this->CorpID."&Pwd=".$this->Pwd."&Mobile=".$Mobile."&Content=".$Contents."&Cell=".$Cell."&SendTime=".$SendTime;
        //GET方式请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查 -https
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_URL, $url.$curpost);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        return $result;
    }

}