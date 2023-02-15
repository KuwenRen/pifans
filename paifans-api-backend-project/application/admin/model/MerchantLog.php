<?php

namespace app\admin\model;

use think\Model;


class MerchantLog extends Model
{

    

    

    // 表名
    protected $name = 'merchant_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    
public function users(){
    return $this->hasOne(User::class,'id','user_id')->field('id,username,avatar,prevtime,logintime,jointime');
}






}
