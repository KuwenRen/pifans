<?php
/**
 * Created by PhpStorm.
 * User: 何明兴
 * Date: 2022/11/11
 * Time: 12:48
 */

namespace app\api\controller;


use app\admin\model\chat\ChatList;
use app\admin\model\chat\Chattype;
use app\api\model\GroupChat;
use app\api\validate\ChatroomValidate;
use app\common\controller\Api;
use app\api\model\GroupChatUsers;
use think\Cache;
use think\cache\driver\Redis;
use think\Db;
use think\Exception;

/**
 * Class Chatroom
 * @package app\api\controller
 * 聊天室
 */
class Chatroom extends Api
{
    protected $noNeedLogin = ['typelist', 'getChatGroupList', 'chatInfo'];
    protected $noNeedRight = '*';

    /**
     * 查询聊天室类型
     */
    public function typelist()
    {
        $result = Chattype::where('id', '>', 0)
            ->order('weigh', 'asc')
            ->select();
        if ($result) {
            foreach ($result as $k => $v) {
                if ($this->language == 2) {
                    $result[$k]['typename'] = $v['typename1'];
                }
                unset($result[$k]['typename1']);
            }
        }
        $this->success('ok', $result);
    }

    /**
     * 获取聊天室列表
     */
    public function getChatGroupList()
    {
        $params = $this->request->param();
        $page = isset($params['page']) ? $params['page'] : 1;
        $limit = isset($params['limit']) ? $params['limit'] : 10;
        $keyword = $params['keyword'];
        if (isset($params['type_id']) && $params['type_id']) {
            $result = GroupChat::where('group_type_id', '=', $params['type_id']);
        } else {
            $result = GroupChat::where('id', '>', 0);
        }
        if (isset($keyword) && $keyword) {
            $result->where('group_name', 'like', '%' . $keyword . '%');
        }
        $totals = $result->count();
        $pages = ceil($totals / $limit);
        $list = $result->limit(($page - 1) * $limit, $limit)->select();
        $redis = new Redis();
        if ($totals > 0) {
            foreach ($list as $k => $v) {
                if ($this->language == 1) {
                    $group_type_name = Chattype::where('id', '=', $v['group_type_id'])
                        ->value('typename');
                } else {
                    $group_type_name = Chattype::where('id', '=', $v['group_type_id'])
                        ->value('typename1');
                }
                $list[$k]['group_type_name'] = $group_type_name;
                $list[$k]['group_user_number'] = GroupChatUsers::where('group_id', '=', $v['id'])->count();
                $isExistKey = $redis->has('room' . $v['id']);
                if ($isExistKey) {
                    //获取最后一次发送消息的时间
                    $totals = $redis->lLen('room' . $v['id']);
                    $data = $redis->lRange('room' . $v['id'], $totals);
                    $record = json_decode($data[$totals - 1], true);
                    $list[$k]['last_send_time'] = isset($record['send_time']) ? $this->getDate(strtotime($record['send_time'])) : '';
                } else {
                    $list[$k]['last_send_time'] = '';
                }
            }
        }
        $this->success('ok', compact('list', 'totals', 'pages'));
    }


    /**
     * 修改群聊名称
     */
    public function updChatGroupName()
    {
        $user = $this->auth->getUserinfo();//用户信息
        $chat_room_id = $this->request->post('chat_room_id');
        if (!isset($chat_room_id) || !$chat_room_id) {
            $this->error('ID有误！');
        }
        $group = GroupChat::where('id', '=', $chat_room_id)->find();
        if (!$group) {
            $this->error(lang('聊天室不存在！'));
        }
        if ($group['is_enable_switch'] == 1) {
            //检查是否是群主或管理员
            $group_manager_ids = explode(',', $group['group_manager_user_ids']);
            if ($user['id'] != $group['group_master_user_id'] && !in_array($user['id'], $group_manager_ids)) {
                return $this->error(lang('无权限操作！'));
            }
        }
        $result = GroupChat::where('id', '=', $chat_room_id)->update([
            'group_name' => $this->request->post('group_name'),
            'updatetime' => time(),
        ]);
        $result ? $this->success('ok') : $this->error();

    }

    /**
     * 修改群聊中-个人信息
     */
    public function updChatuserInfo()
    {
        $params = $this->request->post();
        $user = $this->auth->getUserinfo();//用户信息
        $chat_room_id = $this->request->post('chat_room_id');
        if (!isset($chat_room_id) || !$chat_room_id) {
            $this->error('ID有误！');
        }
        $group = GroupChatUsers::where('group_id', '=', $chat_room_id)
            ->where('user_id', '=', $user['id'])
            ->find();
        if (!$group) {
            $this->error(lang('聊天室不存在,或用户不在此聊天室！'));
        }
        $opea = GroupChatUsers::where('id', '=', $group['id']);
        if (isset($params['avatar']) && $params['avatar']) {
            $upd['avatar'] = $params['avatar'];
        }
        if (isset($params['nickname']) && $params['nickname']) {
            $upd['nickname'] = $params['nickname'];
        }
        $upd['createtime'] = time();
        $result = $opea->update($upd);
        $result ? $this->success('ok') : $this->error();
    }

    public function chatInfo()
    {
        $chat_room_id = $this->request->get('chat_room_id');
        if (!isset($chat_room_id) || !$chat_room_id) {
            $this->error('ID有误！');
        }
        $group = GroupChat::where('id', '=', $chat_room_id)
            ->find();
        if (!$group) {
            $this->error(lang('聊天室不存在！'));
        }
        if ($this->language == 2) {
            $group->typename = Chattype::where('id', '=', $group->group_type_id)->value('typename1');
        } else {
            $group->typename = Chattype::where('id', '=', $group->group_type_id)->value('typename');
        }
        //查询群-成员
        $group->group_users = Db::name('group_chat_users')
            ->alias('a')
            ->where('a.group_id', '=', $group->id)
            ->join('user b', 'b.id=a.user_id', 'left')
            ->field('a.nickname,a.avatar,b.nickname as user_nickname,b.avatar as user_avatar')
            ->select();
        $group->group_users_count = Db::name('group_chat_users')
            ->where('group_id', '=', $group->id)
            ->count();

        $this->success('ok', $group);
    }

    public function changeMaster()
    {
        $params = $this->request->param();
        $result = GroupChat::where('id', '=', $params['group_id'])->find();
        if (!$result) {
            $this->error('聊天室不存在');
        }
        $user_id = $this->auth->id;
        if ($user_id != $result->group_master_user_id) {
            $this->error('无权限操作');
        }
        $result = GroupChat::where('id', '=', $params['group_id'])
            ->update([
                'group_master_user_id' => $params['user_id'],
                'updatetime' => time(),
            ]);
        $result ? $this->success() : $this->error();
    }

    public function getChatUserInfo()
    {
        $params = $this->request->param();
        $result = GroupChat::alias('a')
            ->field('b.nickname,b.avatar')
            ->join('group_chat_users b', 'b.group_id=a.id', 'left')
            ->where('a.id', '=', $params['group_id'])
            ->where('b.user_id', '=', $params['user_id'])
            ->find();
        $result ? $this->success('ok', $result) : $this->error();
    }

    public function editChatUserInfo()
    {
        $params = $this->request->param();
        if (!key_exists('nickname', $params)) {
            $this->error(lang('请输入昵称'));
        }
        if (!key_exists('avatar', $params)) {
            $this->error(lang('请上传头像'));
        }
        $result = GroupChatUsers::where('group_id', '=', $params['group_id'])
            ->where('user_id', '=', $params['user_id'])
            ->update([
                'nickname' => $params['nickname'],
                'avatar' => $params['avatar'],
            ]);
        $result ? $this->success('ok') : $this->error();
    }

    public function getMembers()
    {
        $params = $this->request->param();
        $page = isset($params['page']) ? $params['page'] : 1;
        $limit = isset($params['limit']) ? $params['limit'] : 10;
        $chat_room_id = $this->request->get('chat_room_id');
        if (!isset($chat_room_id) || !$chat_room_id) {
            $this->error('ID有误！');
        }
        $group = GroupChat::where('id', '=', $chat_room_id)
            ->find();
        if (!$group) {
            $this->error(lang('聊天室不存在！'));
        }
        $result = Db::name('group_chat_users')
            ->alias('a')
            ->join('group_chat b', 'b.id=a.group_id')
            ->join('user c', 'c.id=a.user_id')
            ->field('a.group_id,a.nickname,a.user_id,a.avatar,b.group_name,b.status,c.nickname as user_nickname,c.avatar as user_avatar');
        if (isset($params['keyword']) && $params['keyword']) {
            $result->whereLike('a.nickname|b.group_name|c.nickname', '%' . $params['keyword'] . '%');
            $totals = Db::name('group_chat_users')
                ->alias('a')
                ->join('group_chat b', 'b.id=a.group_id')
                ->join('user c', 'c.id=a.user_id')
                ->whereLike('a.nickname|b.group_name|c.nickname', '%' . $params['keyword'] . '%')
                ->count();
        } else {
            $totals = Db::name('group_chat_users')
                ->alias('a')
                ->join('group_chat b', 'b.id=a.group_id')
                ->join('user c', 'c.id=a.user_id')
                ->count();
        }

        $pages = ceil($totals / $limit);
        $list = $result->limit(($page - 1) * $limit, $limit)->select();
        $this->success('ok', compact('list', 'totals', 'pages'));
    }

    public function exitChatroom()
    {
        Db::startTrans();
        try {
            $chat_room_id = $this->request->get('chat_room_id');
            if (!isset($chat_room_id) || !$chat_room_id) {
                throw new Exception('ID有误');
            }
            $group = GroupChat::where('id', '=', $chat_room_id)
                ->find();
            if (!$group) {
                throw new  Exception('聊天室不存在');
            }
            $user = $this->auth->getUserinfo();//用户信息

            $chatUser = new GroupChatUsers();
            //退出群聊
            $tt = $chatUser->where('group_id', '=', $chat_room_id)
                ->where('user_id', '=', $user['id'])
                ->delete();
            if (!$tt) {
                throw new Exception('退出失败');
            }
            $group_user_count = GroupChatUsers::where('group_id', '=', $chat_room_id)->count();
            if ($group_user_count == 0) {
                //删除聊天室
                GroupChat::where('id', '=', $chat_room_id)->delete();
            }
            Db::commit();
            return json(['code' => 1, 'msg' => '操作成功']);
        } catch (\Exception $e) {
            Db::rollback();
            return json(['code' => 0, 'msg' => $e->getMessage()]);
        }

    }

    public function chatTalking()
    {
        $params = $this->request->param();
        $limit = isset($params['limit']) ? $params['limit'] : 10;

        $chat_room_id = $this->request->get('chat_room_id');
        if (!isset($chat_room_id) || !$chat_room_id) {
            $this->error('ID有误');
        }
        $group = GroupChat::where('id', '=', $chat_room_id)
            ->find();
        if (!$group) {
            $this->error('聊天室不存在');
        }
        $totals = Db::name('group_chat_talking')
            ->where('group_id', '=', $chat_room_id)
            ->count();
        $page = isset($params['page']) ? (int)$params['page'] : ceil($totals / $limit);
        $list = Db::name('group_chat_talking')
            ->alias('a')
            ->join('user b', 'b.id=a.user_id', 'left')
            ->field('a.id,a.group_id,a.content,a.create_time,b.nickname,b.avatar')
            ->where('a.group_id', '=', $chat_room_id)
            ->limit(($page - 1) * $limit, $limit)
            ->select();
        if ($list) {
            foreach ($list as $k => $v) {
                $user = Db::name('group_chat_users')
                    ->where('group_id', '=', $v['group_id'])->find();
                if ($user) {
                    if ($user['nickname']) {
                        $list[$k]['nickname'] = $user['nickname'];
                    }
                    if ($user['avatar']) {
                        $list[$k]['avatar'] = $user['avatar'];
                    }
                }
                $list[$k]['create_time'] = $this->getDate($v['create_time']);
            }
        }
        $this->success('查询成功', compact('list', 'page', 'totals'));
    }

    protected function getDate($time)
    {
        $datetime = date('H', $time);
        $strtime = date('H:i', $time);

        $day = date('d', $time);
        $nowday = date('d');

        //判断是否是今日
        if ($day == $nowday) {
            if ($datetime >= 0 && $datetime < 12) {
                $str = '早上';
            } else if ($datetime >= 12 && $datetime < 14) {
                $str = '中午';
            } else {
                $str = '晚上';
            }
            return $str . $strtime;
        }
        if ($nowday - $day == 1) {
            $str = '昨天';
            return $str . $strtime;
        } else {
            $str = date('Y-m-d H:i', $time);
            return $str;
        }
    }

    public function joinGroupChat()
    {
        $user = $this->auth->getUserinfo();//用户信息
        $chat_room_id = $this->request->post('chat_room_id');
        if (!isset($chat_room_id) || !$chat_room_id) {
            $this->error('ID有误');
        }
        $group = GroupChat::where('id', '=', $chat_room_id)
            ->find();
        if (!$group) {
            $this->error('聊天室不存在');
        }
        $isExist = GroupChatUsers::where('group_id', '=', $chat_room_id)
            ->where('user_id', '=', $user['id'])
            ->find();
        if ($isExist) {
            $this->error('您已加入群聊，请勿重复此操作！');
        }
        $chatUser = new GroupChatUsers();
        $chatUser->group_id = $chat_room_id;
        $chatUser->nickname = $user['nickname'];
        $chatUser->user_id = $user['id'];
        $chatUser->join_time = date('Y-m-d H:i:s');
        $chatUser->createtime = time();
        $chatUser->group_id = $chat_room_id;
        if ($chatUser->save()) {
            $this->success('加入成功');
        }
        $this->error('加入失败');
    }

    /**
     * 新建聊天室
     */
    public function addChatGroup()
    {
        $user = $this->auth->getUserinfo();
        $params = $this->request->post();
        $validate = new ChatroomValidate();
        if (!$validate->check($params)) {
            $this->error($validate->getError());
        }
        $time = time();
        Db::startTrans();
        try {
            $params['group_master_user_id'] = $user['id'];
            $params['group_manager_user_ids'] = $user['id'];
            $params['createtime'] = $time;
            $params['updatetime'] = $time;
            $group_id = Db::name('group_chat')->insertGetId($params);
            if (!$group_id) {
                throw new Exception('操作失败');
            }
            //添加群聊人员
            $chat_user['user_id'] = $user['id'];
            $chat_user['group_id'] = $group_id;
            $chat_user['nickname'] = $user['nickname'] ?? $user['username'];
            $chat_user['join_time'] = date('Y-m-d H:i:s', $time);
            $chat_user['createtime'] = $time;
            $chat_user['updatetime'] = $time;
            $insert_chat_user = GroupChatUsers::create($chat_user);
            if (!$insert_chat_user) {
                throw new Exception('操作失败');
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('操作成功！');
    }

}
