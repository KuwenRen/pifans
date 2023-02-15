<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class SeekKnowledge extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'seek_knowledge';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'type_text',
        'is_hot_text',
        'type_text',
        'create_time_text'
    ];

    public function getCreateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['createtime']) ? $data['createtime'] : '');
        return is_numeric($value) ? date('Y-m-d H:i', $value) : '';
    }


    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }


    public function getTypeList()
    {
        return ['4' => __('Type 4'), '1' => __('Type 1'), '2' => __('Type 2'), '3' => __('Type 3')];
    }

    public function getIsHotList()
    {
        return ['0' => __('Is_hot 0'), '1' => __('Is_hot 1')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $list = SeekKnowledgeType::where('id', '=', $data['type'])->value('name');
        return !empty($list) ? $list : '';
    }


    public function getIsHotTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_hot']) ? $data['is_hot'] : '');
        $list = $this->getIsHotList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function knowLedgeComment()
    {
        return $this->hasMany('KnowledgeComment', 'knowledge_id', 'id');
    }


}
