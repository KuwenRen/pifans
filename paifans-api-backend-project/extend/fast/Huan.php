<?php
namespace fast;

use app\admin\model\User;

class Huan
{

    protected $app_key;
    protected $client_id;
    protected $client_secret;
    protected $org_name;
    protected $app_name;
    protected $url;

    public function __construct()
    {
        $this->app_key = '1106220122127174#paifen';
        $this->client_id = 'YXA6eyzR8PcDReyyQSM_cnRvjw';
        $this->client_secret = 'YXA65k2siBkYhqNs1M1IkPN7dkfLhUY';
        $this->org_name = '1106220122127174';
        $this->app_name = 'paifen';
        $this->url = "https://a1.easemob.com/{$this->org_name}/{$this->app_name}";
    }

    //环信注册获取token
    public function hx_token()
    {
        if (cache('user_hx_token')) {
            return cache('user_hx_token');
        }
        $url = $this->url."/token";
        $data = array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret
        );
        $rs = json_decode($this->hx_curl($url, $data), true);
        cache('user_hx_token', $rs['access_token'], 86400);
        return $rs['access_token'];
    }

    //环信注册
    public function hx_register($username)
    {
        $url = $this->url."/users";
        $nickname = \app\common\model\User::where('id',$username)->value('nickname');
        $data = array(
            'username' => $username,
            'password' => '123456a',
            'nickname' => $nickname,
        );
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->hx_token()
        );
        $res = json_decode($this->hx_curl($url, $data, $header, "POST"), true);
        return $res;
    }
    
     //发送文本消息
    public function sendTextMessage()
    {
        $url = $this->url."/messages";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->hx_token()
        );
        $body['target_type']="users";
        $body['target']= ["1"];
        $options['type']="txt";
        $options['msg']="小虎牙";
        $body['msg']=$options;
        $body['from'] = "2";
       
        $b=json_encode($body,320);
        // dd($b);
      
        $result=$this->hx_curl($url,$b,$header,"POST");

        return json_decode($result,true);


    }
    
    //环信设置头像
    public function hx_avatar($username,$option)
    {
        $url = $this->url."/users/{$username}";
        $header = array(
//            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $this->hx_token()
        );
        $res = json_decode($this->hx_curl($url, $option, $header, "PUT"), true);
        return $res;
    }
    
    //获取用户属性
    public function getUserMeta($username)
    {
        $url = $this->url."/metadata/user/{$username}";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->hx_token()
        );
        $res = json_decode($this->hx_curl($url, [], $header, "GET"), true);
     
        return $res;
    }

    //获取应用下所有用户信息
    public function getUsers()
    {
        $url = $this->url."/users";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->hx_token()
        );
        $res = json_decode($this->hx_curl($url,[],$header,'GET'),true);
        return $res;
    }

    //删除某个用户
    public function delUser($username)
    {
        $url = $this->url."/users/{$username}";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->hx_token()
        );
        $res = json_decode($this->hx_curl($url,[],$header,'DELETE'),true);
        return $res;
    }

    //批量删除用户
    public function delUsers($limit)
    {
        $url = $this->url."/users?limit={$limit}";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->hx_token()
        );
        $res = json_decode($this->hx_curl($url,[],$header,'DELETE'),true);
        return $res;
    }

    //创建群组
    public function createGroup($options)
    {
        $url = $this->url."/chatgroups";
    }


    //创建聊天室
    public function createChatRoom($options){
        $url=$this->url."/chatrooms";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->hx_token()
        );
        $result=$this->hx_curl($url,$options,$header);
        // 房间号'room_id'=>$result['data']['id'],
        return $result;
    }

    //聊天室详情
    public function chatRoomDetail($chatroom_id)
    {
        $url = $this->url."/chatrooms/{$chatroom_id}";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->hx_token()
        );
        $res = json_decode($this->hx_curl($url,[],$header,'GET'),true);
        return $res;
    }

    //获取app下所有聊天室
    public function getAllChatRooms()
    {
        $url = $this->url."/chatrooms";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->hx_token()
        );
        $res = json_decode($this->hx_curl($url,[],$header,'GET'),true);
        return $res;
    }

    //删除某个聊天室
    public function delChatRoom($chatroomid)
    {
        $url = $this->url."/chatrooms/{$chatroomid}";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->hx_token()
        );
        $res = json_decode($this->hx_curl($url,[],$header,'DELETE'),true);
        return $res;
    }

    //添加聊天室成员
    function adduserChatRoom($usernames,$chatroomid){

        $url=$this->url."/chatrooms/{$chatroomid}/users/{$usernames}";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->hx_token()
        );
        $result=$this->hx_curl($url,[],$header);
        return $result;
    }


    function hx_curl($url, $data, $header = false, $method = "POST")
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $ret = curl_exec($ch);
        return $ret;
    }



}