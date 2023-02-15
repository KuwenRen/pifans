<?php

namespace app\api\controller;

use app\admin\model\Ecology;
use app\admin\model\EcologyCollectLog;
use app\admin\model\EcologyCommentLog;
use app\admin\model\EcologyLikeLog;
use app\admin\model\Information;
use app\admin\model\SeekKnowledge;
use app\admin\model\SeekKnowledgeType;
use app\admin\model\Telegram;
use app\common\controller\Api;
use think\Db;

class News extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * @param $pid
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取知识分类
     */
    public function getTypeList($pid = null){
        $SeekKnowledgeTypeMode = new SeekKnowledgeType();
        $where = [];
        if($pid){
            $where['pid'] = $pid;
        }else{
            $where['pid'] = ['eq',0];
        }
        $where['status'] = '1';
        // 查询知识分类
        $list = $SeekKnowledgeTypeMode->where($where)->order("weigh desc")->field('id,name')->select();
        $this->success("请求成功！",$list);
    }

    /**
     * @param $keywords
     * @param $page
     * @param $listRows
     * @return void
     * 咨询搜索
     */
    public function getInformationList($page = null,$listRows = null,$keywords = null){
        $InformationMode = new Information();
        $where = [];
        if (!$keywords) {
            $this->error("缺少参数！");
        }
        // 搜索条件
        $where['title1|title2'] = ['like',"%".$keywords."%"];
        // 查询咨询搜素列表
        $list = $InformationMode->where($where)->field('id,title1,title2,content1,content2,gives,watchs,createtime')->paginate($listRows);
        foreach ($list as $v){
            $v['comment_count'] = Db::name('information_comment')->where('information_id',$v['id'])->count();
            $v['createtime']  = date("Y-m-d H:s",$v['createtime']);
        }
        $this->success("请求成功！",$list);
    }

    /**
     * @param $keywords
     * @param $page
     * @param $listRows
     * @return void
     * 生态搜索
     */
    public function getEcologyList($page = null,$listRows = null,$keywords = null){
        $EcologyMode = new Ecology();
        $where = [];
        if (!$keywords) {
            $this->error("缺少参数！");
        }
        // 搜索条件
        $where['title1|title2'] = ['like',"%".$keywords."%"];
        // 查询生态搜索
        $list = $EcologyMode->where($where)->field('id,title1,title2,star,image')->paginate($listRows);
        $this->success("请求成功！",$list);
    }

    /**
     * @param $keywords
     * @param $page
     * @param $listRows
     * @return void
     * 知识搜索
     */
    public function getKnowledgeList($page = null,$listRows = null,$keywords = null){
        $SeekKnowledgeMode = new SeekKnowledge();
        $where = [];
        if (!$keywords) {
            $this->error("缺少参数！");
        }
        // 搜索条件
        $where['title1|title2'] = ['like',"%".$keywords."%"];
        // 查询知识搜索
        $list = $SeekKnowledgeMode->where($where)->field('id,title1,title2,gives,watchs,image,createtime')->paginate($listRows);
        foreach ($list as $v){
            $v['createtime']  = date("Y-m-d",$v['createtime']);
        }
        $this->success("请求成功！",$list);
    }

    /**
     * @param $keywords
     * @param $page
     * @param $listRows
     * @return void
     * 电报搜索
     */
    public function getTelegramList($page = null,$listRows = null,$keywords = null){
        $TelegramMode = new Telegram();
        $where = [];
        if (!$keywords) {
            $this->error("缺少参数！");
        }
        // 搜索条件
        $where['title1|title2'] = ['like',"%".$keywords."%"];
        // 查询电报搜索
        $list = $TelegramMode->with('countrys')->where($where)->field('id,title1,title2,country_id')->paginate($listRows);
        $this->success("请求成功！",$list);
    }

    /**
     * @return void
     * 知识-交易所-子栏目查询
     */
    public function getExchangeList($page = null,$listRows = null,$type = null){
        $SeekKnowledgeMode = new SeekKnowledge();
        if(!$type){
            $this->error("缺少参数！");
        }
        // 查询知识
        $list = $SeekKnowledgeMode->where('type',$type)->field('id,title1,title2,image,gives,watchs,createtime,type')->paginate($listRows);
        foreach ($list as $v){
            $v['createtime'] = date("Y-m-d",$v['createtime']);
        }
        $this->success("请求成功！",$list);

    }

    /**
     * @return void
     * 点赞
     */
    public function setLike($id = null){
        if(!$id){
            $this->error("缺少参数！");
        }

        $EcologyLikeLogMode = new EcologyLikeLog();
        // 查询生态
        $row = $EcologyLikeLogMode->where(['user_id'=>$this->auth->id,'ecology_id'=>$id])->find();
        if($row){
            $this->error("您已点赞过了！");
        }
        // 添加获得积分记录
        set_integral_log(2,$this->auth->id,$id);
        $params = [
            'user_id'=>$this->auth->id,
            'ecology_id'=>$id
        ];
        // 添加点赞记录
        $res = $EcologyLikeLogMode->allowField(true)->save($params);
        if (!$res){
            $this->error("点赞失败！");
        }
        $this->success("点赞成功！");
    }

    /**
     * @return void
     * 取消生态点赞
     */
    public function cancelLike($id = null){
            if(!$id){
                $this->error("缺少参数ID！");
            }
            $EcologyLikeLogMode = new EcologyLikeLog();
            $row = $EcologyLikeLogMode->where(['user_id'=>$this->auth->id,'ecology_id'=>$id])->find();
            if(!$row){
                $this->error("数据不存在！");
            }
            $row->delete();
            $this->success("取消成功！");
    }

    /**
     * @return void
     * 点赞记录列表
     */
    public function getLikeList($page = null,$listRows = null){
        $EcologyLikeLogMode = new EcologyLikeLog();
        // 查询点赞列表
        $list = $EcologyLikeLogMode->with(['ecology','sign'])->order("id desc")->where(['user_id'=>$this->auth->id])->paginate($listRows);
        foreach ($list as $v){
            if(!empty($v->sign)){
                $v->sign->createtime = date("Y-m-d",$v->sign->createtime);
            }
            $v->createtime = date("Y-m-d H:i:s",$v->createtime);
            $v->ecology->collect_count = EcologyCollectLog::where('ecology_id',$v->ecology_id)->count();
            $v->ecology->like_count = EcologyLikeLog::where('ecology_id',$v->ecology_id)->count();
        }
        $this->success("请求成功！",$list);
    }

    /**
     * @return void
     * 生态收藏
     */
    public function setCollect($id = null){
        if(!$id){
            $this->error("缺少参数ID！");
        }
        if(!$id){
            $this->error("缺少参数！");
        }

        $EcologyCollectLogMode = new EcologyCollectLog();
        // 查询生态
        $row = $EcologyCollectLogMode->where(['user_id'=>$this->auth->id,'ecology_id'=>$id])->find();
        if($row){
            $this->error("您已点收藏了！");
        }
        // 添加获得积分记录
        set_integral_log(3,$this->auth->id,$id);
        $params = [
            'user_id'=>$this->auth->id,
            'ecology_id'=>$id
        ];
        // 添加点赞记录
        $res = $EcologyCollectLogMode->allowField(true)->save($params);
        if (!$res){
            $this->error("收藏失败！");
        }
        $this->success("收藏成功！");

    }

    /**
     * @return void
     * 取消生态收藏
     */
    public function cancelCollect($id = null){
        if(!$id){
            $this->error("缺少参数ID！");
        }
        $EcologyCollectLogMode = new EcologyCollectLog();
        $row = $EcologyCollectLogMode->where(['user_id'=>$this->auth->id,'ecology_id'=>$id])->find();
        if(!$row){
            $this->error("数据不存在！");
        }
        $row->delete();
        $this->success("取消成功！");
    }

    /**
     * @return void
     * 收藏记录列表
     */
    public function getCollectList($page = null,$listRows = null){
        $EcologyCollectLogMode = new EcologyCollectLog();
        // 查询点赞列表
        $list = $EcologyCollectLogMode->with(['ecology','sign'])->order("id desc")->where(['user_id'=>$this->auth->id])->paginate($listRows);
        foreach ($list as $v){
            if(!empty($v->sign)){
                $v->sign->createtime = date("Y-m-d",$v->sign->createtime);
            }
            $v->createtime = date("Y-m-d H:i:s",$v->createtime);
            $v->ecology->collect_count = EcologyCollectLog::where('ecology_id',$v->ecology_id)->count();
            $v->ecology->like_count = EcologyLikeLog::where('ecology_id',$v->ecology_id)->count();
        }
        $this->success("请求成功！",$list);
    }

    /**
     * @param $id
     * @return void
     * 生态添加评论
     */
    public function addComment(){
        $params = $this->request->param();
        if(empty($params['ecology_id']) || empty($params['content'])){
            $this->error("缺少参数!");
        }

        $EcologyCommentLogMode = new EcologyCommentLog();
        $row = $EcologyCommentLogMode->where(['user_id'=>$this->auth->id,'ecology_id'=>$params['ecology_id']])->find();
        if($row){
            $this->error("已评论过了！");
        }
        // 获得积分
        set_integral_log(4,$this->auth->id,$params['ecology_id']);
        $params['user_id'] = $this->auth->id; // 用户id
        $res = $EcologyCommentLogMode->allowField(true)->save($params);
        if(!$res){
            $this->error('评论失败！');
        }
        $this->success("评论成功！");

    }

    /**
     * @return void
     * 生态评论列表
     */
    public function getCommentList($page = null,$listRows = null){
        $EcologyCommentLogMode = new EcologyCommentLog();
        // 查询点赞列表
        $list = $EcologyCommentLogMode->with(['ecology','user'])->order("id desc")->where(['user_id'=>$this->auth->id])->paginate($listRows);
        foreach ($list as $v){
            if(!empty($v->sign)){
                $v->sign->createtime = date("Y-m-d",$v->sign->createtime);
            }
            $v->createtime = date("Y-m-d H:i:s",$v->createtime);
            $v->ecology->collect_count = EcologyCollectLog::where('ecology_id',$v->ecology_id)->count();
            $v->ecology->like_count = EcologyLikeLog::where('ecology_id',$v->ecology_id)->count();
            $v->ecology->comment_count = EcologyCommentLog::where('ecology_id',$v->ecology_id)->count();
        }
        $this->success("请求成功！",$list);
    }

    /**
     * @param $id
     * @return void
     * 生态删除
     */
    public function delComment($id = null){
        if(!$id){
            $this->error("缺少参数评论ID！");
        }
        $EcologyCommentLogMode = new EcologyCommentLog();
        // 查询评论
        $row = $EcologyCommentLogMode->where(['user_id'=>$this->auth->id,'id'=>$id])->find();
        if(!$row){
            $this->error("数据不存在！");
        }
        $row->delete();
        $this->success("删除成功！");
    }

    /**
     * @param $id
     * @return void
     * 生态详情评论列表
     */
    public function detailsCommentList($id = null,$page = null,$listRows = null){
        if(!$id){
            $this->error("缺少参数ID！");
        }
        $EcologyCommentLogMode = new EcologyCommentLog();
        $list = $EcologyCommentLogMode->with('user')
            ->order("id desc")
            ->where(['user_id'=>$this->auth->id,'ecology_id'=>$id])->paginate($listRows);
        foreach ($list as $v){
            if(!empty($v->createtime)){
                $v->createtime = date("Y-m-d",$v->createtime);
            }
        }
        $this->success("请求成功！",$list);
    }

}
