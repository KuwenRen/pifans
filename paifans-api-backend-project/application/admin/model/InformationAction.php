<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class InformationAction extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'information_action';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    protected $dateFormat = "Y-m-d H:i:s";

    // 追加属性
    protected $append = [
        'type_text'
    ];
    

    
    public function getTypeList()
    {
        return ['1' => __('Type 1'), '2' => __('Type 2')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function information()
    {
        return $this->belongsTo('Information', 'information_id', 'id', [], 'LEFT')
                    ->bind([
                        'title1' => 'title1',
                        'title2' => 'title2',
                        'image' => 'image',
                        'gives' => 'gives',
                        'watchs' => 'watchs',
                        'information_type' => 'type'

                    ]);

    }
}
