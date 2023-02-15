<?php

use think\Route;

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * web端新路由
 */
Route::group('v1', function () {
    //用户相关
    Route::group('user', function () {
        Route::post('login', 'api/user/login');
        Route::post('updateUserPassword', 'api/user/updateUserPassword');//修改密码
        Route::get('pifansList', 'api/user/pifansList');//派粉列表
        Route::get('myPowerValue', 'api/user/getMyPowerValue');//我的粉力
        Route::get('mycomments', 'api/user/mycomments');//我的评论
        Route::get('mygives', 'api/user/mygives');//我的点赞
        Route::post('updateUserInfo', 'api/user/updateUserInfo');//修改个人信息
        Route::post('updateMechatInfo', 'api/user/updateMechatInfo');//修改商家信息
        Route::get('getSocialAccount', 'api/user/getSocialAccount');//获取社交账号类型
        Route::post('getUserInfo', 'api/user/getUserInfo');//获取用户个人信息
        Route::post('sign', 'api/user/sign');//用户签到
    });
    //web3设置
    Route::group('web', function () {
        Route::get('typelist', 'api/web/typelist');//获取web3类型列表
        Route::get('getWebList', 'api/web/getWebList');//获取web3类型内容
    });
    //pinetwork设置
    Route::group('pinetwork', function () {
        Route::get('typelist', 'api/pinetwork/typelist');//获取pinetwork类型列表
        Route::get('getPinetworkList', 'api/pinetwork/getPinetworkList');//获取pinetwork类型内容
    });
    //聊天室设置
    Route::group('chatroom', function () {
        Route::get('typelist', 'api/chatroom/typelist');//聊天室类型
        Route::get('getChatGroupList', 'api/chatroom/getChatGroupList');//聊天室列表
        Route::post('updChatGroupName', 'api/chatroom/updChatGroupName');//修改聊天室昵称
        Route::post('updChatUserInfo', 'api/chatroom/updChatUserInfo');//修改聊天室-个人信息
        Route::get('chatInfo', 'api/chatroom/chatInfo');//聊天室信息
        Route::get('getMembers', 'api/chatroom/getMembers');//聊天室成员
        Route::get('exitChatroom', 'api/chatroom/exitChatroom');//退出聊天室
        Route::get('chatTalking', 'api/chatroom/chatTalking');//聊天室内容
        Route::post('joinGroupChat', 'api/chatroom/joinGroupChat');//加入聊天室
        Route::post('addChatGroup', 'api/chatroom/addChatGroup');//新建聊天室
    });
    Route::group('file', function () {
        Route::post('uploadAvatar', 'api/file/uploadAvatar');//上传头像
    });
    Route::post('uploadAvatar', 'api/file/uploadAvatar');//上传头像
    //商家
    Route::group('business', function () {
        Route::get('getBusinessType', 'api/business/getBusinessType');
        Route::get('getMerchantType', 'api/business/getMerchantType');
        Route::get('getProvices', 'api/business/getProvices');
        Route::get('getCountry', 'api/business/getCountry');
        Route::get('getMechantList', 'api/business/getMechantList');
        Route::post('mechantInfo', 'api/business/mechantInfo');
        Route::post('createMechant', 'api/business/createMechant');//商家入驻
    });
    //派粉官方
    Route::group('official', function () {
        Route::post('about', 'api/common/about');//官方介绍
        Route::post('leaveMessage', 'api/common/leaveMessage');//反馈
    });
    //商家有关
    Route::group('merchant', function () {
        Route::get('getMerchantType', 'api/merchant/getMerchantType');//支付类型
        Route::get('getCountryList', 'api/merchant/getCountryList');//获取地区
        Route::get('getBusinessType', 'api/merchant/getBusinessType');//获取商家类型
        Route::get('getSocialAccountType', 'api/merchant/getSocialAccountType');//获取社交账号类型
    });
});
return [
    //别名配置,别名只能是映射到控制器且访问时必须加上请求的方法
    '__alias__' => [
    ],
    //变量规则
    '__pattern__' => [
    ],
//        域名绑定到模块
//        '__domain__'  => [
//            'admin' => 'admin',
//            'api'   => 'api',
//        ],
];
