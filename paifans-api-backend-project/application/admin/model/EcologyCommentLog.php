<?php

namespace app\admin\model;

use think\Model;

/**
 * 生态评论表
 */
class EcologyCommentLog extends Model
{
    // 表名
    protected $name = 'ecology_comment_log';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';

    public function ecology(){
        return $this->hasOne(Ecology::class,'id',"ecology_id")->field("id,title1,title2,type");
    }
    public function user(){
        return $this->hasOne(User::class,'id',"user_id")->field("id,username,avatar,user_number,prevtime,logintime,jointime");
    }
}
