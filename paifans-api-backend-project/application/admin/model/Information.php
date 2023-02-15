<?php

namespace app\admin\model;

use app\admin\model\pinetwork\PinetworkType;
use think\Db;
use think\Model;
use traits\model\SoftDelete;

class Information extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'information';

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
        $type = Db::name('pinetwork_type')->where('id', '>', 0)
            ->field('id,typename')
            ->order('id asc')
            ->select();
//        $result = ['1' => __('PINETWORK'), '2' => __('加密货币'), '4' => '免费'];
        if ($type) {
            foreach ($type as $k => $v) {
                $result[$v['id']] = $v['typename'];
            }
        }
        return $result;
    }

    public function getIsHotList()
    {
        return ['0' => __('否'), '1' => __('是')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsHotTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_hot']) ? $data['is_hot'] : '');
        $list = $this->getIsHotList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function informationComment()
    {
        return $this->hasMany('informationComment', 'information_id', 'id');
    }


}
