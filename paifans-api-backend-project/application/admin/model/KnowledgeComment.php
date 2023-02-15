<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class KnowledgeComment extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'knowledge_comment';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text'
    ];


    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }


    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id', [], 'LEFT');
    }


    public function seekknowledge()
    {
        return $this->belongsTo('SeekKnowledge', 'knowledge_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
