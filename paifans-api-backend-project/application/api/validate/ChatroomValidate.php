<?php
namespace app\api\validate;
/**
 * Created by PhpStorm.
 * User: 何明兴
 * Date: 2022/12/11
 * Time: 12:57
 */

class ChatroomValidate extends \think\Validate
{
    protected $rule =   [
        'group_type_id' => 'require|number|gt:0',
        'group_name' => 'require',
        'show_image' => 'require',
        'is_enable_switch' => 'require|number|in:0,1',
    ];

    protected $message  =   [
//        'group_type_id.require' => '聊天室类型不能为空',
//        'group_type_id.integer' => '聊天室类型只能为数字',
//        'group_type_id.gt' => '聊天室类型数字必须大于0',
//        'group_name.require' => '聊天室名称不能为空',
//        'show_image.require' => '聊天室封面不能为空',
//        'is_enable_switch.require' => '是否仅群主/管理员修改群名称不能为空',
//        'is_enable_switch.number' => '是否仅群主/管理员修改群名称只能为数字',
//        'is_enable_switch.in' => '管理员修改群名称必须为0或1的数字',
    ];
}
