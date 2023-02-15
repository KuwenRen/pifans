<?php

namespace app\api\controller;

use app\admin\model\Information;
use app\admin\model\InformationAction;
use app\admin\model\KnowledgeAction;
use app\admin\model\InformationComment;
use app\admin\model\KnowledgeComment;
use app\admin\model\InviteRecord;
use app\admin\model\Merchant;
use app\admin\model\SeekKnowledge;
use app\admin\model\Socialaccount;
use app\admin\model\UserSign;
use app\common\controller\Api;
use app\common\library\Ems;
use app\common\library\Sms;
use fast\Poster;
use fast\Random;
use think\Cache;
use think\cache\driver\Redis;
use think\Config;
use think\Exception;
use think\Session;
use think\Validate;
use function fast\e;
use think\Db;

/**
 * 会员接口
 */
class User extends Api
{
    protected $noNeedLogin = ['getIndexInfo', 'retrieveUserPassword', 'login', 'mobilelogin', 'register', 'resetpwd', 'changeemail', 'changemobile', 'third'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();

        if (!Config::get('fastadmin.usercenter')) {
            $this->error(__('User center already closed'));
        }

    }

    //用户进入直播间
    public function addChatRoomUser()
    {
        $user_id = $this->auth->id;
        $country_name = \app\admin\model\User::where('id', $user_id)->value('city');
        $chat_room_id = country_room_id($country_name, $user_id);
        $this->success(lang('请求成功'), ['chat_room_id' => $chat_room_id]);
    }

    //添加资讯收藏|点赞 (资讯)
    public function addInformationAction()
    {
        $user_id = $this->auth->id;
        $data = $this->request->param();
        $type = $data['type'];
        $action = $data['action'];
        $information_id = $data['information_id'];
        if ($action == 1) {
            //添加
            $res = InformationAction::create([
                'user_id' => $user_id,
                'information_id' => $information_id,
                'type' => $type
            ]);
            if ($res) {
                Db::name('information')->where('id', $information_id)->setInc('gives');
                $this->success(lang('谢谢支持!'));
            }
            $this->error(lang('网络错误!'));
        } else if ($action != 1) {
            //取消
            $res = InformationAction::where(['user_id' => $user_id, 'information_id' => $information_id, 'type' => $type])->delete();
            if ($res) {
                Db::name('information')->where('id', $information_id)->setDec('gives');
                $this->success(lang('取消成功'));
            }
            $this->error(lang('取消失败'));
        }
    }

    //添加资讯收藏|点赞 (知识)
    public function addKnowledgeAction()
    {
        $user_id = $this->auth->id;
        $data = $this->request->param();
        $type = $data['type'];
        $action = $data['action'];
        $knowledge_id = $data['knowledge_id'];
        if ($action == 1) {
            //添加
            $res = KnowledgeAction::create([
                'user_id' => $user_id,
                'knowledge_id' => $knowledge_id,
                'type' => $type
            ]);
            if ($res) {
                Db::name('seek_knowledge')->where('id', $knowledge_id)->setInc('gives');
                $this->success(lang('谢谢支持!'));
            }
            $this->error(lang('网络错误!'));
        } else if ($action != 1) {
            //取消
            $res = KnowledgeAction::where(['user_id' => $user_id, 'knowledge_id' => $knowledge_id, 'type' => $type])->delete();
            if ($res) {
                Db::name('seek_knowledge')->where('id', $knowledge_id)->setDec('gives');
                $this->success(lang('取消成功'));
            }
            $this->error(lang('取消失败'));
        }

    }

    //添加资讯评论
    public function addInformationComment()
    {
        $user_id = $this->auth->id;
        if (!$user_id) {
            $this->error(lang('请登录后操作'));
        }
        $data = $this->request->param();
        $today_begin = date('Y-m-d') . ' 00:00:00';
        $today_end = date('Y-m-d') . ' 23:59:59';
        $isGetPower = InformationComment::where('information_id', '=', $data['information_id'])
            ->where('user_id', '=', $user_id)
//            ->whereBetween('createtime', [strtotime($today_begin), strtotime($today_end)])
            ->find();
        if (!$isGetPower) {
            //添加粉力值记录
            $t1 = \app\admin\model\User::where('id', '=', $user_id)->setInc('powder_value', 5);//增加5粉力值
            if (!$t1) {
                $this->error(lang('提交失败1'));
            }
        }
        $data['user_id'] = $user_id;
        $res = InformationComment::create($data);
        if ($res) {
            //添加粉力值
            $this->success(lang('提交成功'));
        }
        $this->error(lang('提交失败'));
    }


    //添加知识评论
    public function addKnowledgeComment()
    {
        $user_id = $this->auth->id;
        if (!$user_id) {
            $this->error(lang('请登录后操作'));
        }
        $data = $this->request->param();
        $data['user_id'] = $user_id;
        $res = KnowledgeComment::create($data);
        if ($res) {
            $this->success(lang('提交成功'));
        }
        $this->error(lang('提交失败'));
    }

    //删除评论 (资讯)
    public function deleteInformationComment()
    {
        $id = $this->request->param('id');
        $res = InformationComment::where('id', $id)->delete();
        if ($res) {
            $this->success(lang('删除成功'));
        }
        $this->error(lang('删除失败'));
    }

    //删除评论 (知识)
    public function deleteKnowledgeComment()
    {
        $id = $this->request->param('id');
        $res = KnowledgeComment::where('id', $id)->delete();
        if ($res) {
            $this->success(lang('删除成功'));
        }
        $this->error(lang('删除失败'));
    }


    //我的评论 (资讯)
    public function myInformationComment()
    {
        $user_id = $this->auth->id;
        $data = $this->request->param();
        $page = $data['page'];
        $listRow = $data['listRows'];
        $where['user_id'] = $user_id;
        $information_id = InformationComment::where($where)->column('information_id');
        $information_id = array_unique($information_id);
        $res = [];
        if (!$information_id) {
            $this->success(lang('请求成功'), ['total' => 0, 'data' => []]);
        }
        $res = Information::with(['informationComment' => function ($query) use ($user_id) {
            $query->with(['user' => function ($q) {
                $q->field('id,username,avatar,avatar_code,user_number,prevtime,logintime,jointime');
            }])->where('user_id', $user_id);
        }])->where('id', 'in', $information_id)
            ->order('weigh desc,id desc')
            ->page($page)
            ->paginate($listRow);
        $this->success(lang('请求成功'), $res);
    }

    //我的评论(知识)
    public function myKnowledgeComment()
    {
        $user_id = $this->auth->id;
        $data = $this->request->param();
        $page = $data['page'];
        $listRow = $data['listRows'];
        $where['user_id'] = $user_id;
        $knowledge_id = KnowledgeComment::where($where)->column('knowledge_id');
        $knowledge_id = array_unique($knowledge_id);
        $res = [];
        if (!$knowledge_id) {
            $this->success(lang('请求成功'), ['total' => 0, 'data' => []]);
        }
        $res = SeekKnowledge::with(['knowLedgeComment' => function ($query) use ($user_id) {
            $query->with(['user' => function ($q) {
                $q->field('id,username,avatar,avatar_code,user_number,prevtime,logintime,jointime');
            }])->where('user_id', $user_id);
        }])->where('id', 'in', $knowledge_id)
            ->order('weigh desc,id desc')
            ->page($page)
            ->paginate($listRow);
        $this->success(lang('请求成功'), $res);
    }

    //获取知识|资讯 收藏| 点赞总数
    public function getToalNumber()
    {
        $user_id = $this->auth->id;
        $data = $this->request->param();
        $type = $data['type'];//1=收藏,2=点赞
        $where = ['user_id' => $user_id, 'type' => $type];
        $total1 = InformationAction::where($where)->count();
        $total2 = KnowledgeAction::where($where)->count();
        $total_number = $total2 + $total1;

        $this->success(lang('请求成功'), $total_number);

    }

    //我的点赞|收藏记录 (资讯)
    public function myInformationRecord()
    {
        $user_id = $this->auth->id;
        $data = $this->request->param();
        $page = $data['page'];
        $listRow = $data['listRows'];
        $type = $data['type'];
        $where = ['user_id' => $user_id, 'type' => $type];
        $res = InformationAction::with('information')->where($where)->page($page)->paginate($listRow);
        $total_number = InformationAction::where($where)->count();
        $this->success(lang('请求成功'), $res);
    }

    //我的点赞|收藏记录 (知识)
    public function myKnowledgeRecord()
    {
        $user_id = $this->auth->id;
        $data = $this->request->param();
        $page = $data['page'];
        $listRow = $data['listRows'];
        $type = $data['type'];
        $res = KnowledgeAction::with('seekknowledge')->where(['user_id' => $user_id, 'type' => $type])->page($page)->paginate($listRow);
        foreach ($res as $k => $val) {
            $res[$k]['createtime'] = date('Y-m-d H:i:s', $val['createtime']);
        }
        $this->success(lang('请求成功'), $res);
    }

    //我的派粉
    public function mysInviteUserList()
    {
        $user_id = $this->auth->id;
        $data = $this->request->param();
        $page = $data['page'];
        $listRow = $data['listRows'];
        $res = \app\common\model\User::where('pid', $user_id)
            ->field(
                'id,avatar,avatar_code,
                         username,user_number,account,other_account,
                         createtime
                     ')
            ->order('id desc')
            ->page($page)
            ->paginate($listRow);
        foreach ($res as $k => $val) {
            $res[$k]['account'] = substr_replace($val['account'], '****', 3, 4);
        }
        $this->success(lang('请求成功'), $res);
    }

    //我的粉力
    public function myPowerValue()
    {
        $user_id = $this->auth->id;
        $data = $this->request->param();
        $page = $data['page'];
        $listRow = $data['listRows'];
        $res = InviteRecord::with('inviteUser')
            ->where('user_id', $user_id)
            ->order('id desc')
            ->page($page)
            ->paginate($listRow);
        $result = $res->toArray();
        $result['total_powder_value'] = \app\common\model\User::where('id', $user_id)->value('powder_value');
        $this->success(lang('请求成功'), $result);
    }

    public function getMyPowerValue()
    {
        $user_id = $this->auth->id;
        $params = $this->request->param();
        $page = isset($params['page']) ? $params['page'] : 1;
        $limit = isset($params['limit']) ? $params['limit'] : 10;
        $list = InviteRecord::with('inviteUser')
            ->where('user_id', $user_id)
            ->limit(($page - 1) * $limit, $limit)
            ->select();
        $totals = InviteRecord::with('inviteUser')
            ->where('user_id', $user_id)->count();
        $pages = ceil($totals / $limit);
        $this->success('ok', compact('list', 'totals', 'pages'));
    }

    //获取首页信息坐标
    public function getIndexInfo()
    {
        $user_id = $this->auth->id;
        $res = \app\common\model\User::field(
            'id,username,nickname,wx_number,declaration
            avatar,avatar_code,country,province,
            city,lat,log
            ')
            ->orderRaw("rand()")
            ->limit(100)->select();
        $this->success(lang('请求成功'), $res);
    }

    //找回用户密码
    public function retrieveUserPassword()
    {
        $data = $this->request->param();
        $account = $data['account'];
        $user_info = \app\common\model\User::where('account', $account)->find();
        $code = $data['code'];
        $new_password = $data['new_password'];
        $new_password_again = $data['new_password_again'];
        if (!$user_info) {
            $this->error(lang('账号不存在'));
        }
        //校验code
        if (!$check_code = check_code($account, $code, 2)) {
            $this->error(lang('验证码错误'));
        }
        if ($user_info->password == getEncryptPassword($new_password, $user_info->salt)) {
            $this->error(lang('新密码与旧密码一致'));
        }
        if ($new_password != $new_password_again) {
            $this->error(lang('新密码与确认新密码不一致'));
        }
        $salt = Random::alnum();
        $ac_new_password = getEncryptPassword($new_password, $salt);
        $res = \app\admin\model\User::where('account', $account)->update(['salt' => $salt, 'password' => $ac_new_password]);
        if ($res) {
            $this->success(lang('修改成功'));
        }
        $this->error(lang('修改失败'));

    }

    //修改用户密码
    public function updateUserPassword()
    {
        $user_id = $this->auth->id;
        $user_info = \app\common\model\User::where('id', $user_id)->find();
        $data = $this->request->param();
        $old_password = $data['old_password'];
        $new_password = $data['new_password'];
        $new_password_again = $data['new_password_again'];
        if ($user_info->password != getEncryptPassword($old_password, $user_info->salt)) {
            $this->success(lang('旧密码错误'));
        }
        if ($old_password == $new_password) {
            $this->error(lang('新密码与旧密码一致'));
        }
        if ($new_password != $new_password_again) {
            $this->error(lang('新密码与确认新密码不一致'));
        }
        $salt = Random::alnum();
        $ac_new_password = getEncryptPassword($new_password, $salt);
        $res = \app\admin\model\User::where('id', $user_id)->update(['salt' => $salt, 'password' => $ac_new_password]);
        if ($res) {
            $this->success('修改成功');
        }
        $this->error('修改失败');

    }

    //修改用户信息
    public function updateUserInfo()
    {
        $user_id = $this->auth->id;
        $data = $this->request->param();
        if (isset($data['avatar_code'])) {
            $data['avatar'] = get_five_avatar()[$data['avatar_code'] - 1];
        }
        $upd['updatetime'] = time();
        if (isset($data['account']) && $data['account']) {
            $upd['account'] = $data['account'];
        }
        if (isset($data['declaration']) && $data['declaration']) {
            $upd['declaration'] = $data['declaration'];
        }
        if (isset($data['email']) && $data['email']) {
            $upd['email'] = $data['email'];
        }
        if (isset($data['nickname']) && $data['nickname']) {
            $upd['nickname'] = $data['nickname'];
        }
        if (isset($data['wx_number']) && $data['wx_number']) {
            $upd['wx_number'] = $data['wx_number'];
        }
        if (isset($data['region']) && $data['region']) {
            $upd['region'] = $data['region'];
        }
        if (isset($data['gender']) && $data['gender']) {
            $upd['gender'] = (int)$data['gender'];
        }
        $res = \app\admin\model\User::where('id', $user_id)->update($upd);
        if ($res) {
            $this->success(lang('修改成功'));
        }
        $this->error(lang('修改失败'));
    }

    //用户分享码
    public function shareCode()
    {
        $user_id = $this->auth->id;
        $user_info = \app\admin\model\User::where('id', $user_id)->field('invite_code,share_code,avatar')->find();
        $share_code = $user_info->share_code;
        $invite_code = $user_info->invite_code;
        // if (!$share_code) {
        $url = host() . '/register?invite_code=' . $invite_code;
        $poster = new Poster();
        $share_code = $poster->create($url, $invite_code, $user_info['avatar']);
        $share_code = substr($share_code, 1);
        //更新用户分享码
        \app\admin\model\User::where('id', $user_id)->update(['share_code' => $share_code]);
        // }
        $this->success('请求成功', $share_code);
    }

    //获取用户信息
    public function getUserInfo()
    {
        $user_id = $this->auth->id;
        if (!$user_id) {
            $this->error(lang('请登录后操作'));
        }
        $res = $this->auth->getUserinfo();
        //判断今日是否已签到
        $today_begin = strtotime(date('Y-m-d') . ' 00:00:00');
        $today_end = strtotime(date('Y-m-d') . ' 23:59:59');
        $signed = UserSign::where('user_id', '=', $user_id)
            ->where('type', '=', 1)
            ->whereBetween('createtime', [$today_begin, $today_end])
            ->find();
        if ($signed) {
            $res['signed'] = 1;
        } else {
            $res['signed'] = 0;
        }
        $this->success(lang('请求成功'), $res);
    }

    //用户登录
    public function login()
    {
        $account = $this->request->post('account');
        $password = $this->request->post('password');

        $ret = $this->auth->login($account, $password);
        if ($ret) {
            $res = $this->auth->getUserinfo();
            // if($res){
            //     $res['user_number'] = $res['user_number'] . '｜' .  '邀请码：'.$res['invite_code'];
            // }
            $data = ['userinfo' => $res];
            $this->success(lang('登录成功'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 手机验证码登录
     *
     * @ApiMethod (POST)
     * @param string $mobile 手机号
     * @param string $captcha 验证码
     */
    public function mobilelogin()
    {
        $mobile = $this->request->post('mobile');
        $captcha = $this->request->post('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (!Sms::check($mobile, $captcha, 'mobilelogin')) {
            $this->error(__('Captcha is incorrect'));
        }
        $user = \app\common\model\User::getByMobile($mobile);
        if ($user) {
            if ($user->status != 'normal') {
                $this->error(__('Account is locked'));
            }
            //如果已经有账号则直接登录
            $ret = $this->auth->direct($user->id);
        } else {
            $ret = $this->auth->register($mobile, Random::alnum(), '', $mobile, []);
        }
        if ($ret) {
            Sms::flush($mobile, 'mobilelogin');
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注册会员
     *
     * @ApiMethod (POST)
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email 邮箱
     * @param string $mobile 手机号
     * @param string $code 验证码
     */
    public function register()
    {
        $type = $this->request->param('type'); //1=大陆,2=港澳台,3=国际
        $password = $this->request->post('password');
        $account = $this->request->param('account');
        $code = $this->request->post('code');
        $invite_code = $this->request->param('invite_code');
        $country = $this->request->param('country');

        if ($type == 1) {
            if (\app\admin\model\User::where('account', $account)->find()) {
                $this->error(lang('该账号已存在'));
            }
        } else {
            if (\app\admin\model\User::where('other_account', $account)->find()) {
                $this->error(lang('该账号已存在'));
            }
        }

        if ($type == 1) {
            if ($account && !Validate::regex($account, "/^1[3456789]{1}\d{9}$/")) {
                $this->error(lang('手机格式错误'));
            }
        } else {
            if ($account && !Validate::is($account, "email")) {
                $this->error(lang('邮箱格式错误'));
            }
        }
        //校验code
        if (!$check_code = check_code($account, $code, 1)) {
            $this->error(lang('验证码错误'));
        }
        $extend['pid'] = 0;
        //校验邀请码
        if ($invite_code) {
            if (!$pid = \app\admin\model\User::where('invite_code', $invite_code)->value('id')) {
                $this->error(lang('上级不存在'));
            } else {
                $extend['pid'] = $pid;
            }
        }
        //用户昵称,用户头像随机
        $username = 'Username:' . Random::alpha(6);
//        $avatar = '/assets/none.png';
        $images = \app\common\model\Config::where('group', 'user_img')->column('value');
        $avatar_code = rand(1, 5);
        $avatar = $images[$avatar_code - 1];
        $extend['avatar'] = $avatar;
        $extend['avatar_code'] = $avatar_code;
        $extend['country'] = $country;
        if (!$username || !$password) {
            $this->error(__('Invalid parameters'));
        }
        $ret = $this->auth->register($username, $password, $account, $extend);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            //
            $this->success(__('Sign up successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 退出登录
     * @ApiMethod (POST)
     */
    public function logout()
    {
        if (!$this->request->isPost()) {
            $this->error(__('Invalid parameters'));
        }
        $this->auth->logout();
        $this->success(__('Logout successful'));
    }

    /**
     * 修改会员个人信息
     *
     * @ApiMethod (POST)
     * @param string $avatar 头像地址
     * @param string $username 用户名
     * @param string $nickname 昵称
     * @param string $bio 个人简介
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        $username = $this->request->post('username');
        $nickname = $this->request->post('nickname');
        $bio = $this->request->post('bio');
        $avatar = $this->request->post('avatar', '', 'trim,strip_tags,htmlspecialchars');
        if ($username) {
            $exists = \app\common\model\User::where('username', $username)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('Username already exists'));
            }
            $user->username = $username;
        }
        if ($nickname) {
            $exists = \app\common\model\User::where('nickname', $nickname)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('Nickname already exists'));
            }
            $user->nickname = $nickname;
        }
        $user->bio = $bio;
        $user->avatar = $avatar;
        $user->save();
        $this->success();
    }

    /**
     * 修改邮箱
     *
     * @ApiMethod (POST)
     * @param string $email 邮箱
     * @param string $captcha 验证码
     */
    public function changeemail()
    {
        $user = $this->auth->getUser();
        $email = $this->request->post('email');
        $captcha = $this->request->post('captcha');
        if (!$email || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if (\app\common\model\User::where('email', $email)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Email already exists'));
        }
        $result = Ems::check($email, $captcha, 'changeemail');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->email = 1;
        $user->verification = $verification;
        $user->email = $email;
        $user->save();

        Ems::flush($email, 'changeemail');
        $this->success();
    }

    /**
     * 修改手机号
     *
     * @ApiMethod (POST)
     * @param string $mobile 手机号
     * @param string $captcha 验证码
     */
    public function changemobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->post('mobile');
        $captcha = $this->request->post('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Mobile already exists'));
        }
        $result = Sms::check($mobile, $captcha, 'changemobile');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->mobile = 1;
        $user->verification = $verification;
        $user->mobile = $mobile;
        $user->save();

        Sms::flush($mobile, 'changemobile');
        $this->success();
    }

    /**
     * 第三方登录
     *
     * @ApiMethod (POST)
     * @param string $platform 平台名称
     * @param string $code Code码
     */
    public function third()
    {
        $url = url('user/index');
        $platform = $this->request->post("platform");
        $code = $this->request->post("code");
        $config = get_addon_config('third');
        if (!$config || !isset($config[$platform])) {
            $this->error(__('Invalid parameters'));
        }
        $app = new \addons\third\library\Application($config);
        //通过code换access_token和绑定会员
        $result = $app->{$platform}->getUserInfo(['code' => $code]);
        if ($result) {
            $loginret = \addons\third\library\Service::connect($platform, $result);
            if ($loginret) {
                $data = [
                    'userinfo' => $this->auth->getUserinfo(),
                    'thirdinfo' => $result
                ];
                $this->success(__('Logged in successful'), $data);
            }
        }
        $this->error(__('Operation failed'), $url);
    }

    /**
     * 重置密码
     *
     * @ApiMethod (POST)
     * @param string $mobile 手机号
     * @param string $newpassword 新密码
     * @param string $captcha 验证码
     */
    public function resetpwd()
    {
        $type = $this->request->post("type");
        $mobile = $this->request->post("mobile");
        $email = $this->request->post("email");
        $newpassword = $this->request->post("newpassword");
        $captcha = $this->request->post("captcha");
        if (!$newpassword || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if ($type == 'mobile') {
            if (!Validate::regex($mobile, "^1\d{10}$")) {
                $this->error(__('Mobile is incorrect'));
            }
            $user = \app\common\model\User::getByMobile($mobile);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Sms::check($mobile, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Sms::flush($mobile, 'resetpwd');
        } else {
            if (!Validate::is($email, "email")) {
                $this->error(__('Email is incorrect'));
            }
            $user = \app\common\model\User::getByEmail($email);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Ems::check($email, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Ems::flush($email, 'resetpwd');
        }
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($newpassword, '', true);
        if ($ret) {
            $this->success(__('Reset password successful'));
        } else {
            $this->error($this->auth->getError());
        }
    }

    public function pifansList()
    {
        $params = $this->request->param();
        $user_id = $this->auth->id;
        $page = isset($params['page']) ? $params['page'] : 1;
        $limit = isset($params['limit']) ? $params['limit'] : 20;

        $list = \app\admin\model\User::where('pid', '=', $user_id)
            ->field('nickname,user_number,account,email,jointime,prevtime,logintime')
            ->limit(($page - 1) * $limit, $limit)
            ->select();
        $totals = \app\admin\model\User::where('pid', '=', $user_id)->count();
        $pages = ceil($totals / $limit);
        $this->success('ok', compact('list', 'totals', 'pages'));
    }

    public function mycomments()
    {
        $params = $this->request->param();
        $user_id = $this->auth->id;
        $page = isset($params['page']) ? $params['page'] : 1;
        $limit = isset($params['limit']) ? $params['limit'] : 20;
        $type = isset($params['type']) ? $params['type'] : 0;
        if (!in_array($type, [0, 1])) {
            $this->error('类型有误！');
        }
        if ($type == 0) {
            $information = new Information();
            $list = $information->alias('a')
                ->field('a.id,a.title1,a.title2,a.image,a.type,a.gives,a.watchs,a.createtime')
                ->join('information_comment b', 'b.information_id=a.id', 'LEFT')
                ->where('b.user_id', '=', $user_id)
                ->group('a.id')
                ->limit(($page - 1) * $limit, $limit)
                ->select();

            $totals = $information
                ->alias('a')
                ->join('information_comment b', 'b.information_id=a.id', 'LEFT')
                ->where('b.user_id', '=', $user_id)
                ->count();
        } else {
            $konwledge = new SeekKnowledge();
            $list = $konwledge->alias('a')
                ->field('a.id,a.title1,a.title2,a.image,a.type,a.gives,a.watchs,a.createtime')
                ->join('knowledge_comment b', 'b.knowledge_id=a.id', 'LEFT')
                ->where('b.user_id', '=', $user_id)
                ->group('a.id')
                ->limit(($page - 1) * $limit, $limit)
                ->select();

            $totals = $konwledge
                ->alias('a')
                ->join('knowledge_comment b', 'b.knowledge_id=a.id', 'LEFT')
                ->where('b.user_id', '=', $user_id)
                ->count();
        }
        if ($totals > 0) {
            if ($list) {
                foreach ($list as $k => $v) {
                    if ($type == 0) {
                        $comments = InformationComment::where('a.information_id', '=', $v['id'])
                            ->alias('a')
                            ->join('user b', 'b.id=a.user_id', 'left')
                            ->order('a.id desc')
                            ->where('a.user_id', '=', $user_id)
                            ->field('a.id as comment_id,a.user_id,a.content,a.createtime,b.user_number,b.avatar')
                            ->select();
                    } else {
                        $comments = KnowledgeComment::where('knowledge_id', '=', $v['id'])
                            ->alias('a')
                            ->join('user b', 'b.id=a.user_id', 'left')
                            ->order('a.id desc')
                            ->where('a.user_id', '=', $user_id)
                            ->field('a.id as comment_id,a.user_id,a.content,a.createtime,b.user_number,b.avatar')
                            ->select();
                    }

                    $list[$k]['comments'] = $comments;
                    if ($this->language == 1) {
                        $list[$k]['title'] = $v['title1'];
//                        $list[$k]['content'] = $v['content1'];
                    } else {
                        $list[$k]['title'] = $v['title2'];
//                        $list[$k]['content'] = $v['content2'];
                    }
                    unset($list[$k]['title1']);
                    unset($list[$k]['title2']);
//                    unset($list[$k]['content1']);
//                    unset($list[$k]['content2']);

                }
            }
        }
        $pages = ceil($totals / $limit);
        $this->success('ok', compact('list', 'totals', 'pages'));
    }

    public function mygives()
    {
        $params = $this->request->param();
        $user_id = $this->auth->id;
        $page = isset($params['page']) ? $params['page'] : 1;
        $limit = isset($params['limit']) ? $params['limit'] : 20;
        $type = isset($params['type']) ? $params['type'] : 0;
        $isgive = isset($params['isgive']) ? $params['isgive'] : 1;
        if (!in_array($type, [0, 1])) {
            $this->error('类型1有误！');
        }
        if (!in_array($isgive, [1, 2])) {
            $this->error('类型2有误！');
        }
        if ($type == 0) {
            $list = InformationAction::where('a.user_id', '=', $user_id)
                ->where('a.type', '=', $isgive)
                ->alias('a')
                ->limit(($page - 1) * $limit, $limit)
                ->field('b.id,b.title1,b.title2,b.image,b.type,b.gives,b.watchs,b.createtime')
                ->join('information b', 'a.information_id=b.id', 'left')
                ->select();
            $totals = InformationAction::where('a.user_id', '=', $user_id)
                ->where('a.type', '=', $isgive)
                ->alias('a')
                ->join('information b', 'a.information_id=b.id', 'left')
                ->count();
        } else {
            $list = KnowledgeAction::where('a.user_id', '=', $user_id)
                ->where('a.type', '=', $isgive)
                ->alias('a')
                ->field('b.id,b.title1,b.title2,b.image,b.type,b.gives,b.watchs,b.createtime')
                ->join('seek_knowledge b', 'a.knowledge_id=b.id', 'left')
                ->limit(($page - 1) * $limit, $limit)
                ->select();
            $totals = KnowledgeAction::where('a.user_id', '=', $user_id)
                ->where('a.type', '=', $isgive)
                ->alias('a')
                ->join('seek_knowledge b', 'a.knowledge_id=b.id', 'left')
                ->count();
        }
        if ($totals > 0) {
            foreach ($list as $k => $v) {
                if ($this->language == 1) {
                    $list[$k]['title'] = $v['title1'];
                } else {
                    $list[$k]['title'] = $v['title2'];
                }
                unset($list[$k]['title1']);
                unset($list[$k]['title2']);
            }
        }
        $pages = ceil($totals / $limit);
        $this->success('ok', compact('list', 'totals', 'pages'));
    }

    public function getSocialAccount()
    {
        $list = Socialaccount::all();
        $this->success('ok', $list);
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 编辑商家信息
     */
    public function updateMechatInfo()
    {
        $param = $this->request->post();
        $user_id = $this->auth->id;
        if (!$user_id) {
            $this->error(lang('请登录后操作'));
        }
        if (isset($param['merchant_id']) && $param['merchant_id']) {
            $list = Merchant::where('id', '=', $param['merchant_id'])
                ->where('user_id', '=', $user_id)
                ->find();
            if (!$list) {
                $this->error('商家信息不存在！');
            }
        }
        if (isset($param['name']) && $param['name']) {
            $upd['name'] = $param['name'];
        }
        if (isset($param['image']) && $param['image']) {
            $image = json_decode($param['image'], true);
            $param['image'] = $image[0];
            $param['images'] = implode(',', $image);
        }
        if (isset($param['phone']) && $param['phone']) {
            $upd['phone'] = $param['phone'];
        }
        if (isset($param['address']) && $param['address']) {
            $upd['address'] = $param['address'];
            $gdMapKey = config('map_key');
            // 组装请求路径
            $mapUrl = 'https://restapi.amap.com/v3/geocode/geo?address=' . $param['address'] . '&key=' . $gdMapKey;
            // 获取数据
            $mapResult = file_get_contents($mapUrl);
            // 解析数据
            $mapResult = json_decode($mapResult, true);
            if ((int)$mapResult['status'] == 1) {
                // 结构数据
                $mapData = $mapResult['geocodes'][0];
                $upd['province'] = $mapData['province'];
                $upd['city'] = $mapData['city'];
                $upd['area'] = $mapData['district'];
                // 解析定位信息
                if (isset($mapData['location']) && trim($mapData['location']) != '') {
                    $locationAddress = explode(',', trim($mapData['location']));
                    $upd['lng'] = $locationAddress[0];
                    $upd['lat'] = $locationAddress[1];
                }
            }
        }

        if (isset($param['country_id']) && $param['country_id']) {
            $upd['country_id'] = (int)$param['country_id'];
        }
        if (isset($param['business_type_id']) && $param['business_type_id']) {
            $upd['business_type_id'] = (int)$param['business_type_id'];
        }
        if (isset($param['merchant_type_id']) && $param['merchant_type_id']) {
            $upd['merchant_type_id'] = (int)$param['merchant_type_id'];
        }
        if (isset($param['main_goods']) && $param['main_goods']) {
            $upd['main_goods'] = $param['main_goods'];
        }
        if (isset($param['describe']) && $param['describe']) {
            $upd['describe'] = $param['describe'];
        }
        if (isset($param['social_account_type']) && $param['social_account_type']) {
            $upd['social_account_type'] = (int)$param['social_account_type'];
        }
        if (isset($param['social_account']) && $param['social_account']) {
            $upd['social_account'] = $param['social_account'];
        }
        $upd['updatetime'] = time();
        $resultd = Merchant::where('id', '=', $list->id)->update($upd);
        $resultd ? $this->success() : $this->error();
    }

    public function ranking()
    {

    }

    /**
     * 用户签到
     */
    public function sign()
    {
        $user_id = $this->auth->id;
        if (!$user_id) {
            $this->error(lang('请登录后操作'));
        }
        //判断今日是否已签到
        $today_begin = strtotime(date('Y-m-d') . ' 00:00:00');
        $today_end = strtotime(date('Y-m-d') . ' 23:59:59');
        $signed = UserSign::where('user_id', '=', $user_id)
            ->where('type', '=', 1)
            ->whereBetween('createtime', [$today_begin, $today_end])
            ->find();
        if ($signed) {
            $this->error('今日已签到');
        }
        Db::startTrans();
        try {
            $result = UserSign::create([
                'user_id' => $user_id,
                'integral' => 1,
                'createtime' => time(),
                'type' => 1
            ]);
            $t1 = \app\admin\model\User::where('id', '=', $user_id)->setInc('powder_value', 5);//增加5粉力值
            $t2 = \app\admin\model\User::where('id', '=', $user_id)->setInc('score', 1);//增加1积分
            if (!$t1 || !$t2 || !$result) {
                throw new Exception('签到失败');
            }
            Db::commit();//提交事务
            return json(['code' => 1, 'msg' => lang('签到成功')]);
        } catch (\Exception $e) {
            Db::rollback();
            $this->error(lang($e->getMessage()));
        }
    }
}
