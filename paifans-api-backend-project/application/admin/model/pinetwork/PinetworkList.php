<?php

namespace app\admin\model\pinetwork;

use think\Model;


class PinetworkList extends Model
{


    // 表名
    protected $name = 'pinetwork_list';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'create_time_text'
    ];

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    public function getCreateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['createtime']) ? $data['createtime'] : '');
        return $value ? date("Y.m.d", strtotime($value)) : $value;
    }


    public function pinetworktype()
    {
        return $this->belongsTo(Pinetworktype::class, 'type_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
