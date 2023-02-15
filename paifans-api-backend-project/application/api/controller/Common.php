<?php

namespace app\api\controller;

use app\admin\model\Country;
use app\admin\model\Ecology;
use app\admin\model\EcologyCollectLog;
use app\admin\model\EcologyLikeLog;
use app\admin\model\Information;
use app\admin\model\InformationAction;
use app\admin\model\KnowledgeAction;
use app\admin\model\InformationComment;
use app\admin\model\KnowledgeComment;
use app\admin\model\InviteRecord;
use app\admin\model\Message;
use app\admin\model\Notice;
use app\admin\model\SeekKnowledge;
use app\admin\model\SeekKnowledgeType;
use app\admin\model\Telegram;
use app\admin\model\Banner;
use app\common\controller\Api;
use app\common\exception\UploadException;
use app\common\library\Email;
use app\common\library\Upload;
use app\common\model\Area;
use app\common\model\Version;
use fast\Poster;
use fast\Random;
use think\Config;
use think\Hook;
use think\cache\driver\Redis;
use fast\Huan;
use think\Validate;
use think\Db;

// header('Access-Control-Allow-Origin:*');

/**
 * 公共接口
 */
class Common extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * 加载初始化
     *
     * @param string $version 版本号
     * @param string $lng 经度
     * @param string $lat 纬度
     */
    public function init()
    {
        if ($version = $this->request->request('version')) {
            $lng = $this->request->request('lng');
            $lat = $this->request->request('lat');

            //配置信息
            $upload = Config::get('upload');
            //如果非服务端中转模式需要修改为中转
            if ($upload['storage'] != 'local' && isset($upload['uploadmode']) && $upload['uploadmode'] != 'server') {
                //临时修改上传模式为服务端中转
                set_addon_config($upload['storage'], ["uploadmode" => "server"], false);

                $upload = \app\common\model\Config::upload();
                // 上传信息配置后
                Hook::listen("upload_config_init", $upload);

                $upload = Config::set('upload', array_merge(Config::get('upload'), $upload));
            }

            $upload['cdnurl'] = $upload['cdnurl'] ? $upload['cdnurl'] : cdnurl('', true);
            $upload['uploadurl'] = preg_match("/^((?:[a-z]+:)?\/\/)(.*)/i", $upload['uploadurl']) ? $upload['uploadurl'] : url($upload['storage'] == 'local' ? '/api/common/upload' : $upload['uploadurl'], '', false, true);

            $content = [
                'citydata' => Area::getCityFromLngLat($lng, $lat),
                'versiondata' => Version::check($version),
                'uploaddata' => $upload,
                'coverdata' => Config::get("cover"),
            ];
            $this->success('', $content);
        } else {
            $this->error(__('Invalid parameters'));
        }
    }


    public function test()
    {
        $huan = new Huan();
        dd($huan->sendTextMessage());
//          dd($huan->adduserChatRoom(2,171574405693442));
        //  dd($huan->chatRoomDetail(171589647794178));
        // dd($huan->getAllChatRooms());
        // dd($huan->delChatRoom(171589647794177));//171574669934593  171574405693442
//          dd($huan->createChatRoom([
//              'name' => '南京市',
//              'description' => '南京市直播间',
//              'owner' => "2",
//          ]));
        //创建聊天室
        //  dd($huan->delUser(1));
        //   dd($huan->getUserMeta(2));
        //  dd($huan->getUsers());//获取应用下所有用户
        //  dd($huan->hx_register(1));//注册用户
//         dd(user_number(99));


//        $res = getLocationByIp('36.61.52.240');
//        $rectangle = explode(';',$res['rectangle'])[0];
//        $rectangle = explode(',',$rectangle);
//        $params['lat'] = $rectangle[1];
//        $params['log'] = $rectangle[0];
//        dd($res);

//        user_invite_record(2,4);
//        $url = 'http://www.baidu.com';
//        $userName = 'Username:KLWEkd';
//        $title = '分享码';
//        dd(Poster::create($url,$userName,$title));
//          dd(addresstolatlag('江苏省南京市区中山陵'));
//        dd(getCity('222.94.218.192'));
    }

    //3d标签云
    public function label()
    {
        $search = $this->request->param('search');
        $filter['nickname'] = ['neq', 'not null'];
        $res = \app\common\model\User::where('nickname', 'like', '%' . $search . '%')->where($filter)->order('createtime desc')->limit(314)->select();
        $this->success(lang('请求成功'), $res);
    }

    //资讯知识推荐
    public function hotList()
    {
        $res = [];
        $information = Information::field('*,(gives+watchs)/2 total_weigh')
            ->where(['is_hot' => 1])
            ->order('total_weigh desc,weigh desc,id desc')
            ->limit(3)
            ->select();
        foreach ($information as $k => $val) {
            $information[$k]['createtime'] = date('Y-m-d H:i:s', $val['createtime']);
        }
        $res['information'] = $information;
        $knowledge = SeekKnowledge::field('*,(gives+watchs)/2 total_weigh')
            ->where(['is_hot' => 1])
            ->order('total_weigh desc,weigh desc,id desc')
            ->limit(3)
            ->select();
        foreach ($knowledge as $k1 => $val1) {
            $knowledge[$k1]['createtime'] = date('Y-m-d H:i:s', $val1['createtime']);
        }
        $res['knowledge'] = $knowledge;
        $this->success(lang('请求成功'), $res);

    }

    //生态列表
    public function ecologyList()
    {
        $data = $this->request->param();
        $user_id = $this->auth->id;
        $page = isset($data['page']) ? (int)$data['page'] : 1;
        $listRow = isset($data['listRows']) ? (int)$data['listRows'] : 20;
        $type = isset($data['type']) ? (int)$data['type'] : 1;
        $res = Ecology::where('type', $type)->order('star desc,weigh desc,id desc')->page($page)->paginate($listRow);
        foreach ($res as $k => $val) {
            $where['user_id'] = $user_id;
            $where['eid'] = $val['id'];
            $is_score = Db::name('score')->where($where)->count('id');
            if ($is_score > 0) {
                $res[$k]['is_pfen'] = 1;
            } else {
                $res[$k]['is_pfen'] = 2;
            }
        }
        $this->success(lang('请求成功'), $res);
    }

    //生态评分
    public function score()
    {
        $data = $this->request->param();
        $user_id = $this->auth->id;
        if (!$user_id) {
            return json(['code' => 401, 'msg' => '请登录！']);
        }
        $id = intval($data['id']);
        $sc = intval($data['sco']);

        //判断是否点击
        $where['eid'] = $id;
        $map['user_id'] = $user_id;
        $r = Db::name('score')->where($where)->where($map)->count('id');
        if ($r > 0) {
            return json(['code' => 401, 'msg' => '已评,请不要重复点击！']);
        }
        //评分记录数据
        $dataScore = [
            'user_id' => $user_id,
            'eid' => $id,
            'score' => $sc,
            'createtime' => time()
        ];

        // 启动事务
        Db::startTrans();
        try {
            Db::table('fa_score')->insert($dataScore);
            Db::table('fa_ecology')->where('id', $id)->setInc('comment_number');
            //计算评分
            $all_sco = Db::table('fa_score')->where($where)->sum('score');
            $all_uid = Db::table('fa_score')->where($where)->count('user_id');
            $all_star = $all_sco / $all_uid;
            Db::table('fa_ecology')->where('id', $id)->update(['star' => $all_star]);
            // 提交事务
            Db::commit();
            return json(['code' => 1, 'msg' => '评分成功！']);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(['code' => 401, 'msg' => '评分失败！']);
        }
    }

    //评论列表
    public function commentList()
    {
        $data = $this->request->param();
        $id = $data['id'];
        $page = $data['page'];
        $listRow = $data['listRows'];
        $type = $data['type'];
        //类型:1=资讯,2=求知
        $res = [];
        if ($type == 1) {
            $res = informationComment::with('user')->where('information_id', $id)->order("id desc")->page($page)->paginate($listRow);
        } else {
            $res = KnowledgeComment::with('user')->where('knowledge_id', $id)->order("id desc")->page($page)->paginate($listRow);
        }

        $this->success(lang('请求成功'), $res);
    }

    //生态详情
    public function ecologyDetail()
    {
        $id = $this->request->param('id');
        $res = Ecology::get($id);
        // 是否收藏
        $res->is_collect = EcologyCollectLog::where(['user_id' => $this->auth->id, 'ecology_id' => $id])->count('id');
        // 是否点赞
        $res->is_like = EcologyLikeLog::where(['user_id' => $this->auth->id, 'ecology_id' => $id])->count('id');
        $this->success('请求成功', $res);
    }

    //资讯详情
    public function informationDetail()
    {
        $user_id = $this->auth->id;
        $is_collect = 0;
        $is_give = 0;
        $data = $this->request->param();
        $id = $data['id'];
        $type = $data['type'];
        if ($type) {
            if ($type == 1) {
                //上一篇
                $id = Information::where('id', '<', $id)->order('id asc')->limit(1)->value('id');
            } else {
                //下一篇
                $id = Information::where('id', '>', $id)->order('id asc')->limit(1)->value('id');
            }
        }
        if (!$id) {
            $this->error(lang('数据不存在'), '');
        }
        $pre_id = Information::where('id', '<', $id)->order('id asc')->limit(1)->value('id');
        $next_id = Information::where('id', '>', $id)->order('id asc')->limit(1)->value('id');
        $res = Information::with(['informationComment' => function ($query) {
            $query->with('user')->limit(3)->order('weigh desc,id desc');
        }])->where('id', $id)
            ->find();
        if (!$res) {
            $this->error(lang('数据不存在'), '');
        }
        if ($user_id) {
            //类型:1=收藏,2=点赞
            //用户是否收藏
            if (InformationAction::where(['user_id' => $user_id, 'information_id' => $id, 'type' => 1])->find()) {
                $is_collect = 1;
            }
            //用户是否点赞
            if (InformationAction::where(['user_id' => $user_id, 'information_id' => $id, 'type' => 2])->find()) {
                $is_give = 1;
            }
        }
        $res->is_collect = $is_collect;
        $res->is_give = $is_give;
        $res->pre_id = $pre_id ? $pre_id : 0;
        $res->next_id = $next_id ? $next_id : 0;

        //评论总数
        $res->comments = informationComment::where(['status' => 1, 'information_id	' => $id])->count();

        /*详情观看量+1*/
        Information::where('id', $id)->setInc('watchs', 1);


        $this->success(lang('请求成功'), $res);
    }

    //知识详情
    public function knowledgeDetail()
    {
        $user_id = $this->auth->id;
        $is_collect = 0;
        $is_give = 0;
        $data = $this->request->param();
        $id = $data['id'];
        $type = $data['type'];
        if ($type) {
            if ($type == 1) {
                //上一篇
                $id = SeekKnowledge::where('id', '<', $id)->order('id asc')->limit(1)->value('id');
            } else {
                //下一篇
                $id = SeekKnowledge::where('id', '>', $id)->order('id asc')->limit(1)->value('id');
            }
        }
        if (!$id) {
            $this->success(lang('请求成功'), '');
        }
        $pre_id = SeekKnowledge::where('id', '<', $id)->order('id asc')->limit(1)->value('id');
        $next_id = SeekKnowledge::where('id', '>', $id)->order('id asc')->limit(1)->value('id');
        $res = SeekKnowledge::with(['knowLedgeComment' => function ($query) {
            $query->with('user')->limit(3)->order('weigh desc,id desc');
        }])->where('id', $id)
            ->find();
        if ($user_id) {
            //类型:1=收藏,2=点赞
            //用户是否收藏
            if (KnowledgeAction::where(['user_id' => $user_id, 'knowledge_id' => $id, 'type' => 1])->find()) {
                $is_collect = 1;
            }
            //用户是否点赞
            if (KnowledgeAction::where(['user_id' => $user_id, 'knowledge_id' => $id, 'type' => 2])->find()) {
                $is_give = 1;
            }
        }
        $res->is_collect = $is_collect;
        $res->is_give = $is_give;
        $res->pre_id = $pre_id ? $pre_id : 0;
        $res->next_id = $next_id ? $next_id : 0;

        //评论总数
        $res->comments = knowLedgeComment::where(['status' => 1, 'knowledge_id' => $id])->count();

        /*详情观看量+1*/
        SeekKnowledge::where('id', $id)->setInc('watchs', 1);

        $this->success(lang('请求成功'), $res);
    }

    //电报列表
    public function telegraphList()
    {
        $data = $this->request->param();
        $search = $data['search'];
        $page = $data['page'];
        $listRow = $data['listRows'];
        $where = [];
        if ($search) {
            $where['title1|title2'] = array('like', '%' . $search . '%');
        }
        if (isset($data['country_id'])) {
            $country_id = $data['country_id'];
            $where['country_id'] = $country_id;
        }
        $result = [];
        $res = Telegram::with('country')->order('weigh desc,id desc')->where($where)->page($page)->paginate($listRow);

        $result['data'] = $res;
        $result['hot_data'] = Telegram::with('country')->where('is_hot', 1)->where($where)->order('weigh desc,id desc')->limit(5)->select();

        $this->success(lang('请求成功'), $result);
    }

    //资讯列表
    public function informationList()
    {
        $data = $this->request->param();
        $page = $data['page'];
        $listRow = $data['listRows'];
        if (isset($data['type']) && $data['type'] >= 0) {
            $type = isset($data['type']) ? (int)$data['type'] : 0;
            $res = Information::with('informationComment')->order('weigh desc,id desc')->where('type', $type)->page($page)->paginate($listRow);
            $result['data'] = $res;
            $hot_data = Information::with('informationComment')
                ->field('*,(gives+watchs)/2 total_weigh')
                ->where(['is_hot' => 1, 'type' => $type])
                ->order('total_weigh desc,weigh desc,id desc')
                ->limit(7)
                ->select();
            $count = Information::with('informationComment')
                ->where(['is_hot' => 1, 'type' => $type])
                ->count();
            $result['hot_data'] = $hot_data;
            $result['typename']=Db::name('pinetwork_type')->where('id', '=', $type)
                ->where('status', '=', 1)
                ->field('typename,typename1')->find();
        } else {
            $keyword = isset($data['keyword']) ? $data['keyword'] : '';
            if ($keyword) {
                if ($this->language == 1) {
                    $res = Information::with('informationComment')
                        ->order('weigh desc,id desc')
                        ->where('title1', 'like', '%' . $keyword . '%')
                        ->limit(($page - 1) * $listRow, $listRow)
                        ->select();
                    $count = Information::with('informationComment')
                        ->where('title1', 'like', '%' . $keyword . '%')
                        ->count();
                } else {
                    $res = Information::with('informationComment')
                        ->order('weigh desc,id desc')
                        ->where('title2', 'like', '%' . $keyword . '%')
                        ->limit(($page - 1) * $listRow, $listRow)
                        ->select();
                    $count = Information::with('informationComment')
                        ->where('title2', 'like', '%' . $keyword . '%')
                        ->count();
                }

            } else {
                $res = Information::with('informationComment')
                    ->order('weigh desc,id desc')
                    ->limit(($page - 1) * $listRow, $listRow)
                    ->select();
                $count = Information::with('informationComment')->count();
            }

            $result['list'] = $res;
        }
        $result['totals'] = $count;
        $this->success(lang('请求成功'), $result);
    }

    //求知列表
    public function knowledgeList()
    {
        $data = $this->request->param();
        $page = $data['page'];
        $listRow = $data['listRows'];
        $type = $data['type'];
        $where = [];
        if ($type == 2) {
            $ids = SeekKnowledgeType::where(['pid' => $type])->column("id");
            $where['type'] = ['in', $ids];
        } else {
            $where['type'] = $type;
        }
        $res = SeekKnowledge::order('weigh desc,id desc')->where($where)->page($page)->paginate($listRow);
        $result['data'] = $res;
        $hot_data = SeekKnowledge::with('knowLedgeComment')
            ->field('*,(gives+watchs)/2 total_weigh')
            ->where(['is_hot' => 1])
            ->where($where)
            ->order('weigh desc,id desc')
            ->limit(7)
            ->select();
        $result['hot_data'] = $hot_data;

        $this->success(lang('请求成功'), $result);
    }

    //平台公告
    public function notice()
    {
        $data = $this->request->param();
        $page = $data['page'];
        $listRow = $data['listRows'];
        if (isset($data['notice_type'])) {
            $notice_type = $data['notice_type'];
        } else {
            $notice_type = 1;
        }
        $res = Notice::order('weigh desc,id desc')->where('notice_type', $notice_type)->page($page)->paginate($listRow);
        $this->success(lang('请求成功'), $res);
    }

    //国家列表
    public function country()
    {
        // $think_var = $_COOKIE['think_var'];
        // $language = 1;
        // if ($think_var == 'en-us') {
        //     $language = 2;
        // }
        $language = $this->request->header('language');
        $res = Country::order('weigh desc,id desc')->select();
        foreach ($res as $key => $value) {
            $res[$key]['name'] = $value['name' . $language];
        }
        $this->success(lang('请求成功'), $res);
    }

    //留言
    public function leaveMessage()
    {
        $user_id = $this->auth->id;
        $data = $this->request->param();
        if (!isset($data['content']) || !$data['content']) {
            $this->error('请上传反馈内容');
        }
        $data['user_id'] = $user_id;
        $res = Message::create($data);
        if ($res) {
            $this->success('提交成功');
        }
        $this->error('提交失败');
    }

    //用户五种头像列表
    public function userFiveImages()
    {
        $res = [];
        $res['images'] = \app\common\model\Config::where('group', 'user_img')->column('value');
        $this->success('请求成功', $res);
    }

    //关于我们
    public function about()
    {
        $res = [];
        //logo图片
        $res['app_logo'] = config('site.app_logo');
        //关于我们
        if ($this->language == 1) {
            $res['company_about'] = config('site.company_about');
        } else {
            $res['company_about'] = config('site.company_about_en');
        }
        $this->success(lang('请求成功'), $res);
    }

    //发送短信
    public function sentSms()
    {
        $data = $this->request->param();
        $account = $data['account'];
        // if (!isMobile($account)) {
        //     $this->error(lang('手机格式错误'));
        // }
        if ($account && !Validate::regex($account, "/^1[3456789]{1}\d{9}$/")) {
            $this->error(lang('手机格式错误'));
        }
        $event = $data['event'];
        $code = Random::numeric(4);
        $sms = new \fast\Sms();
        $res = $sms->send($account, $code);
        if ($res > 0) {
            create_sms($account, $event, $code);
            $this->success(lang('发送成功'));
        }
        $this->error(lang('发送失败'));
    }

    //banner图
    public function getbanner()
    {
        $res = \app\admin\model\Banner::where('status', 'normal')->limit(5)->field('id,title,urls,images,en_image')->order('createtime desc')->select();
        $this->success(lang('请求成功'), $res);
    }

    /**
     * 发送邮件
     */
    public function sentEmail()
    {
        $data = $this->request->param();
        $account = $data['account'];
        if (!isEmail($account)) {
            $this->error(lang('邮箱格式错误'));
        }
        $event = $data['event'];
        $code = Random::numeric(4);
        $email = new Email();
        $res = $email->subject('验证码')
            ->to($account)
            ->message('验证码为: ' . $code)
            ->send();
        if ($res !== false) {
            create_sms($account, $event, $code);
            $this->success(lang('发送成功'), $code);
        } else {
            $this->error(lang('发送失败'));
        }
    }

    /**
     * 上传文件
     * @ApiMethod (POST)
     * @param File $file 文件流
     */
    public function upload()
    {
        Config::set('default_return_type', 'json');
        //必须设定cdnurl为空,否则cdnurl函数计算错误
        Config::set('upload.cdnurl', '');
        $chunkid = $this->request->post("chunkid");
        if ($chunkid) {
            if (!Config::get('upload.chunking')) {
                $this->error(__('Chunk file disabled'));
            }
            $action = $this->request->post("action");
            $chunkindex = $this->request->post("chunkindex/d");
            $chunkcount = $this->request->post("chunkcount/d");
            $filename = $this->request->post("filename");
            $method = $this->request->method(true);
            if ($action == 'merge') {
                $attachment = null;
                //合并分片文件
                try {
                    $upload = new Upload();
                    $attachment = $upload->merge($chunkid, $chunkcount, $filename);
                } catch (UploadException $e) {
                    $this->error($e->getMessage());
                }
                $this->success(__('Uploaded successful'), ['url' => $attachment->url, 'fullurl' => cdnurl($attachment->url, true)]);
            } elseif ($method == 'clean') {
                //删除冗余的分片文件
                try {
                    $upload = new Upload();
                    $upload->clean($chunkid);
                } catch (UploadException $e) {
                    $this->error($e->getMessage());
                }
                $this->success();
            } else {
                //上传分片文件
                //默认普通上传文件
                $file = $this->request->file('file');
                try {
                    $upload = new Upload($file);
                    $upload->chunk($chunkid, $chunkindex, $chunkcount);
                } catch (UploadException $e) {
                    $this->error($e->getMessage());
                }
                $this->success();
            }
        } else {
            $attachment = null;
            //默认普通上传文件
            $file = $this->request->file('file');
            try {
                $upload = new Upload($file);
                $attachment = $upload->upload();
            } catch (UploadException $e) {
                $this->error($e->getMessage());
            }

            $this->success(lang('上传成功'), $attachment->url);
        }
    }

    //获取app版本号
    public function getAppEdit()
    {
        $appurl = 'http://pifans.app/uploads/pifans.apk';
        return json(['code' => 200, 'ben' => '1.1.8', 'Download_address' => $appurl]);
    }

    //获取备案号
    public function getRecordNumber()
    {
        $urls = 'https://beian.miit.gov.cn';
        $micp = Config::get('site.beian');
        $data = [
            'micp' => $micp,
            'urls' => $urls,
        ];
        $this->success(lang('请求成功'), $data);
    }

    /**
     * 获取知识类型
     */
    public function getKnowledgeType()
    {
        $result = SeekKnowledgeType::where('pid', 0)
            ->order('id', 'desc')
            ->select();
        $this->success($result);
    }

    public function getHotInformationList()
    {
        $data = $this->request->param();
        $page = isset($data['page']) ? (int)$data['page'] : 1;
        $listRow = isset($data['limit']) ? (int)$data['limit'] : 10;
        $result = Information::where(['is_hot' => 1])
            ->field('*,(gives+watchs)/2 total_weigh')
            ->order('total_weigh desc,weigh desc,id desc')
            ->limit(($page - 1) * $listRow, $listRow)
            ->select();
        if ($result) {
            foreach ($result as $k => $v) {
                if ($this->language == 2) {
                    $result[$k]['title'] = $v['title2'];
                    $result[$k]['content'] = $v['content2'];
                } else {
                    $result[$k]['title'] = $v['title1'];
                    $result[$k]['content'] = $v['content1'];
                }
                unset($result[$k]['title1']);
                unset($result[$k]['title2']);
                unset($result[$k]['content1']);
                unset($result[$k]['content2']);
            }
        }
        $this->success(lang('请求成功'), $result);
    }
}
