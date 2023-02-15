<?php

namespace app\api\controller;

use app\admin\model\Businesstype;
use app\admin\model\ConsensusPrice;
use app\admin\model\Country;
use app\admin\model\MerchantLog;
use app\admin\model\MerchantType;
use app\admin\model\Socialaccount;
use app\common\controller\Api;
use think\Db;

/**
 * 商家
 */
class Merchant extends Api
{

    protected $noNeedLogin = ['getMerchantType', 'getMerchantList', 'getConsensusPrice'];
    protected $noNeedRight = '*';

    /**
     * Merchant模型对象
     * @var \app\admin\model\Merchant
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Merchant;
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取地区
     */
    public function getCountryList()
    {
        $Country = new Country();
        $result = $Country->field('id,name1,name2')->select();
        if ($result) {
            foreach ($result as $k => $v) {
                if ($this->language == 2) {
                    $result[$k]['name1'] = $v['name2'];
                }
                unset($result[$k]['name2']);
            }
        }
        $this->success($result);
    }

    /**
     * 获取商家类型
     */
    public function getBusinessType()
    {
        $type = new Businesstype();
        $result = $type->field('id,typename,typename1')->select();
        if ($result) {
            foreach ($result as $k => $v) {
                if ($this->language == 2) {
                    $result[$k]['typename'] = $v['typename1'];
                }
                unset($result[$k]['typename1']);
            }
        }
        $this->success($result);
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取社交账号列表
     */
    public function getSocialAccountType()
    {
        $type = new Socialaccount();
        $result = $type->field('id,name')->select();
        $this->success($result);
    }


    /**
     * @return void
     * 商家列表
     */
    public function getMerchantList($country_id = null, $merchant_type_id = null, $lat = null, $lng = null)
    {
        $where = [];
        // 国家筛选搜索条件
        if ($country_id) {
            $where['country_id'] = $country_id;
        }
        // 支付类型筛选条件
        if ($merchant_type_id) {
            $where['merchant_type_id'] = $merchant_type_id;
        }
        $result = get_range($lat, $lng, 300000);
        //  查询商家
        $list = $this->model->where($where)
            ->where('lat', 'between', [$result['minLat'], $result['maxLat']])
            ->where('lng', 'between', [$result['minLon'], $result['maxLon']])
            ->field('id,name,province,city,area,image,address,lat,lng,status,user_id,business_type_id')
            ->limit(100)
            ->select();
        foreach ($list as $v) {
            $v['province_text'] = Db::name('area')->where('id', '=', $v['province'])->value("name");
            $v['city_text'] = Db::name('area')->where('id', '=', $v['city'])->value("name");
            $v['area_text'] = Db::name('area')->where('id', '=', $v['area'])->value("name");
        }
        $this->success("请求成功！", ['list' => $list, 'count' => $this->model->count()]);

    }

    /**
     * @return void
     * 支付类型
     */
    public function getMerchantType()
    {
        $MerchantTypeMode = new MerchantType();
        // 查询支付类型
        $list = $MerchantTypeMode->where('status', '=', '1')->order('weigh desc')->field('id,name,en_name')->select();
        if ($list) {
            foreach ($list as $k => $v) {
                if ($this->language == 2) {
                    $list[$k]['name'] = $v['en_name'];
                }
                unset($list[$k]['en_name']);
            }
        }
        $this->success("亲求成功！", $list);
    }

    /**
     * @return void
     * 添加商家
     */
    public function addMerchant()
    {
        $params = $this->request->param();
        if (empty($params['name'])) {
            $this->error("名称不能为空！");
        }
        $params['status'] = 1; // 审核中
        $params['user_id'] = $this->auth->id; // 用户id
        $res = $this->model->allowField(true)->save($params);
        if (!$res) {
            $this->error("添加失败！");
        }
        $this->success("添加成功！");
    }

    /**
     * @return void
     * 编辑商家
     */
    public function upMerchant()
    {
        $params = $this->request->param();
        if (empty($params['id'])) {
            $this->error("缺少参数！");
        }
        if (empty($params['name'])) {
            $this->error("名称不能为空！");
        }
        // 查询认领成功的商家
        $row = $this->model->where(['id' => $params['id'], 'user_id' => $this->auth->id, 'status' => '3'])->find();
        if (!$row) {
            $this->error("该商家未认领，不能编辑！");
        }
        $res = $row->allowField(true)->save($params);
        if (!$res) {
            $this->error("修改失败！");
        }
        $this->success("修改成功！");
    }

    /**
     * @return void
     * 认领
     */
    public function upClaim($id = null)
    {
        // 查询认领的商家
        $row = $this->model->where(['id' => $id, 'status' => '0'])->find();
        if (!$row) {
            $this->error("商家不存在或已被认领！");
        }
        $params = [
            'user_id' => $this->auth->id,
            'status' => '1'
        ];
        // 添加认领
        $res = $row->allowField(true)->save($params);
        if (!$res) {
            $this->error("认领失败！");
        }
        $this->success("认领中，等待管理员审核！");

    }

    /**
     * @return void
     * 评论
     */
    public function comment()
    {
        $params = $this->request->param();
        if (empty($params['merchant_id']) || empty($params['comment'])) {
            $this->error("缺少参数！");
        }
        $merchantLogMode = new MerchantLog();
        $params['user_id'] = $this->auth->id; // 用户id
        // 查询评论
        $res = $merchantLogMode->allowField(true)->save($params);
        if (!$res) {
            $this->error("评论失败！");
        }
        $this->success("评论成功！");
    }

    /**
     * @param $id
     * @return void
     * 商家详情
     */
    public function merchantDetail($id = null)
    {
        if (!$id) {
            $this->error("缺少参数！");
        }
        $merchantLogMode = new MerchantLog();
        // 查询商家
        $row = $this->model->where('id', '=', $id)->field('id,name,describe,address,user_id,status')->find();
        // 统计评论
        $row['count'] = $merchantLogMode->where('merchant_id', '=', $id)->count();
        // 默认评分
        $score = 2.5;
        if ($row['count']) {
            // 计算评分
            $score = number_format($merchantLogMode->where('merchant_id', '=', $id)->sum('score') / $row['count'], 1);
        }
        $row['score'] = $score;
        $this->success("请求成功！", $row);

    }

    /**
     * @return void
     * 评论列表
     */
    public function merchantLog($id = null, $page = null, $limit = null)
    {
        if (!$id) {
            $this->error("缺少参数!");
        }
        $merchantLogMode = new MerchantLog();
        // 查询评论
        $list = $merchantLogMode->with('users')->where('merchant_id', '=', $id)->field('id,user_id,score,comment')->paginate($limit);
        $this->success("请求成功！", $list);
    }

    /**
     * 读取省市区数据,联动列表
     */
    public function area()
    {
        $params = $this->request->get();
        if (!empty($params)) {
            $province = isset($params['province']) ? $params['province'] : '';
            $city = isset($params['city']) ? $params['city'] : '';
        } else {
            $province = $this->request->get('province', '');
            $city = $this->request->get('city', '');
        }
        $where = ['pid' => 0, 'level' => 1];
        $provincelist = null;
        if ($province !== '') {
            $where['pid'] = $province;
            $where['level'] = 2;
            if ($city !== '') {
                $where['pid'] = $city;
                $where['level'] = 3;
            }
        }
        $provincelist = Db::name('area')->where($where)->field('id as value,name')->select();
        $this->success('', '', $provincelist);
    }

    /**
     * @return void
     * 共识价提交
     */
    public function addConsensusPrice()
    {
        $ConsensusPriceMode = new ConsensusPrice();
        $params = $this->request->post();
        // 判断必填
        if (empty($params['value'])) {
            $this->error("缺少参数！");
        }
        $params['user_id'] = $this->auth->id; // 用户ID
        // 查询今天是否提交过
        $row = $ConsensusPriceMode->where(['user_id' => $this->auth->id])->whereTime('createtime', 'today')->find();
        if ($row) {
            $this->success("您今天已提交了！");
        }
        // 保存数据
        $res = $ConsensusPriceMode->allowField(true)->save($params);
        if (!$res) {
            $this->error("保存失败！");
        }
        $this->success("保存成功！");
    }

    /**
     * @return void
     * 获取共识价平均值
     */
    public function getConsensusPrice()
    {
        $ConsensusPriceMode = new ConsensusPrice();
        // 平均值
        $value = $ConsensusPriceMode->avg('value');
        $this->success("请求成功！", $value);
    }

}
