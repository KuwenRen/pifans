<?php

namespace app\admin\model;

use think\Model;

/**
 * 生态点赞表
 */
class EcologyLikeLog extends Model
{
    // 表名
    protected $name = 'ecology_like_log';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';

    public function ecology(){
        return $this->hasOne(Ecology::class,'id',"ecology_id")->field("id,title1,title2,type");
    }

    public function sign(){
        return $this->hasOne(UserSign::class,'ecology_id',"ecology_id")
            ->where('type','=',2)
            ->field("id,integral,ecology_id,createtime");
    }
}
