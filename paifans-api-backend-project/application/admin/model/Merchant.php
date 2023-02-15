<?php

namespace app\admin\model;

use think\Model;


class Merchant extends Model
{


    // 表名
    protected $name = 'merchant';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'business'
    ];


    public function getStatusList()
    {
        return ['0' => __('待认领'), '1' => __('审核中'), '2' => __('未通过'), '3' => __('已认领')];
    }

    public function getAuditList()
    {
        return ['2' => __('未通过'), '3' => __('已通过')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getBusinessAttr($val, $data)
    {
        $ids = explode(',', $data['business_type_id']);
        $result = (new Businesstype())->field('id,typename,typename1')->whereIn('id', $ids)->column('typename');
        return implode('/', $result);
    }


}
