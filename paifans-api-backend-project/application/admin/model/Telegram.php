<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Telegram extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'telegraph';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }
    
    public function getIsHotList()
    {
        return ['0' => __('Is_hot 0'), '1' => __('Is_hot 1')];
    }

    







    public function country()
    {
        return $this->belongsTo('Country', 'country_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
