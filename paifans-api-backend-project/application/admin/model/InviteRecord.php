<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class InviteRecord extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'invite_record';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';
    protected $dateFormat = "Y-m-d H:i:s";

    // 追加属性
    protected $append = [

    ];


    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function inviteUser()
    {
        return $this->belongsTo('User', 'to_user_id', 'id', [], 'LEFT')
            ->bind([
                'to_user_name'=>'username',
                'avatar_code' => 'avatar_code',
                'account' => 'account',
                'user_number' => 'user_number'
            ]);
    }


}
