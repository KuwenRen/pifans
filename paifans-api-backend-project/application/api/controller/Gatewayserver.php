<?php

namespace app\api\controller;

use app\admin\model\Chatroom;
use app\admin\model\UserChatRecord;
use app\common\controller\Api;
use GatewayClient\Gateway;

/**
 * 聊天
 */
class Gatewayserver extends Api
{
//    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        Gateway::$registerAddress = '127.0.0.1:1236';
    }

    /**
     * @return void
     * 绑定UID
     */
    public function bind(){
        $client_id = input('post.client_id');
        $room_id = input('post.room_id');
        $uid      = $this->auth->id;
        // client_id与uid绑定
        Gateway::bindUid($client_id, $uid);
        // 加入某个群组（可调用多次加入多个群组）
        Gateway::joinGroup($client_id, $room_id);
        $this->success("绑定成功！");
    }

    /**
     * 发送消息
     * @return void
     */
    public function send(){
        $msg = input('post.msg');
        $room_id = input('post.room_id');
        $params = [
            'user_id'=>$this->auth->id,
            'username'=>$this->auth->username,
            'avatar' => $this->auth->avatar,
            'msg'=>$msg,
            'room_id'=>$room_id,
            'send_time'=>date("Y-m-d H:s:i",time())
        ];
        if(!empty($this->auth->is_mute)){
            $this->error("已被禁言！");
        }
        $chatRecordMode = new UserChatRecord();
        // 保存聊天记录
        $chatRecordMode->allowField(true)->save($params);
        $params['id'] = $chatRecordMode->id; // 聊天id
        // 向任意群组的网站页面发送数据
        Gateway::sendToGroup($room_id,\GuzzleHttp\json_encode($params));
        $this->success("发送成功！",$params);
    }

    /**
     * @return void
     * 获取省份聊天室
     */
    public function getProvinceList(){

        $chatroomMode = new Chatroom();
        // 查询聊天室
        $list = $chatroomMode->where(['type'=>3,'status'=>"1"])->field('id,name')->select();

        $this->success("请求成功！",$list);
    }

    /**
     * @return void
     * 获取聊天记录
     */
    public function getChatRecordList($page = null,$listRows = null,$room_id = null,$id = null){

        if(!$room_id){
            $this->error("缺少参数房间号ID!");
        }
        // 房间号id
        $where['room_id'] = $room_id;
        // 判断id是否存在查其之前的数据
        if($id){
            $where['id'] = ['lt',$id];
        }
        $chatRecordMode = new UserChatRecord();
        // 查询当前聊天记录之前的数据
        $list = $chatRecordMode->where($where)->order("id desc")->paginate($listRows);

        $this->success("获取成功！",$list);
    }

}