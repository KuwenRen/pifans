<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Notice extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'notice';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'type_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2')];
    }
    
    public function getNoticeTypeList()
    {
        return ['1' => __('NoticeType 1'), '2' => __('NoticeType 2')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getNoticeTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['notice_type']) ? $data['notice_type'] : '');
        $list = $this->getNoticeTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
