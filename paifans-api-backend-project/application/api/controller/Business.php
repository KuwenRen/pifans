<?php
/**
 * Created by PhpStorm.
 * User: 何明兴
 * Date: 2022/11/13
 * Time: 15:35
 */

namespace app\api\controller;

use app\admin\model\Businesstype;
use app\admin\model\Country;
use app\admin\model\Merchant;
use app\admin\model\MerchantLog;
use app\admin\model\MerchantType;
use app\admin\model\Socialaccount;
use app\common\controller\Api;
use think\Db;

/**
 * Class Social
 * @package app\api\controller
 * 商家
 */
class Business extends Api
{
    protected $noNeedRight = '*';
    protected $noNeedLogin = '*';

    public function getBusinessType()
    {
        $list = Businesstype::all();
        if ($list) {
            foreach ($list as $k => $v) {
                if ($this->language == 1) {
                    $list[$k]['typename'] = $v['typename'];
                } else {
                    $list[$k]['typename'] = $v['typename1'];
                }
                unset($list[$k]['typename1']);
            }
        }
        $this->success('ok', $list);
    }

    public function getMerchantType()
    {
        $list = MerchantType::where('status', '=', 1)->select();
        if ($list) {
            foreach ($list as $k => $v) {
                if ($this->language == 1) {
                    $list[$k]['typename'] = $v['name'];
                } else {
                    $list[$k]['typename'] = $v['en_name'];
                }
                unset($list[$k]['en_name']);
                unset($list[$k]['name']);
            }
        }
        $this->success('ok', $list);
    }

    public function getProvices()
    {
        $list = Db::name('area')->field('id,name,pinyin')
            ->where('pid', '=', 0)
            ->select();
        $this->success('ok', $list);
    }

    public function getCountry()
    {
        $result = new Country();
        $param = $this->request->get();
        if (isset($param['keyword']) && $param['keyword']) {
            $result->whereLike('name1|name2', '%' . $param['keyword'] . '%');
        }
        $list = $result->field('id,name1,name2')->select();
        if ($list) {
            foreach ($list as $k => $v) {
                if ($this->language == 1) {
                    $list[$k]['name'] = $v['name1'];
                } else {
                    $list[$k]['name'] = $v['name2'];
                }
                unset($list[$k]['name2']);
                unset($list[$k]['name1']);
            }
        }
        $this->success('ok', $list);
    }

    public function getMechantList()
    {
        $result = new Merchant();
        $data = $result;
        $param = $this->request->get();
        $page = isset($param['page']) ? (int)$param['page'] : 1;
        $limit = isset($param['limit']) ? (int)$param['limit'] : 10;
        if (isset($param['keyword']) && $param['keyword']) {
            $data = $result->whereLike('name', '%' . $param['keyword'] . '%');
        }
        if (isset($param['business_type_id']) && $param['business_type_id']) {
//            $data = $result->where('find_in_set(' . (int)$param['business_type_id'] . ',business_type_id)');
            $data = $result->where('business_type_id', '=', (int)$param['business_type_id']);
        }
        if (isset($param['province']) && $param['province']) {
            $data = $result->whereLike('province', '%' . $param['province'] . '%');
        }
        $list = $data->field('id,name,image,merchant_type_id,business_type_id,phone,country_id,province,describe,watch_number')
            ->limit(($page - 1) * $limit, $limit)
            ->order('id', 'desc')
            ->select();
        $totals = count($list);
        $pages = ceil($totals / $limit);
        if ($list) {
            foreach ($list as $k => $v) {
                $type_id = explode(',', $v['business_type_id']);
                $list[$k]['provice_name'] = $v['province'];
                if ($this->language == 1) {
                    $list[$k]['distance'] = '未知';
                    $list[$k]['country_name'] = Country::where('id', '=', $v['country_id'])->value('name1');
                    $typename = Businesstype::whereIn('id', $type_id)
                        ->column('typename');
                    $list[$k]['business_type_name'] = implode('/', $typename);
                } else {
                    $list[$k]['distance'] = 'Unkonwn';
                    $list[$k]['country_name'] = Country::where('id', '=', $v['country_id'])->value('name2');
                    $typename = Businesstype::whereIn('id', $type_id)
                        ->column('typename1');
                    $list[$k]['business_type_name'] = implode('/', $typename);
                }

                if (isset($param['lng']) && $param['lng'] && isset($param['lat']) && $param['lat']) {
                    $list[$k]['distance'] = getDistance($param['lng'], $param['lat'], $area->lng, $area->lat);
                }
            }
        }
        $this->success('ok', compact('list', 'totals', 'pages'));
    }

    public function mechantInfo()
    {
        $user_id = $this->auth->id;
        if (!$user_id) {
            $this->error('请先登录！');
        }
        $param = $this->request->post();
        if (!isset($param['merchant_id']) || !$param['merchant_id']) {
            $this->error('商店ID有误！');
        }
        $result = Merchant::where('id', '=', $param['merchant_id'])
//            ->where('user_id', '=', $user_id)
            ->find();
        if (!$result) {
            $this->error('商店不存在，或您没有权限访问当前商店！');
        }

        $result->social_account_type_list = Socialaccount::where('id', '=', $result->social_account_type)
            ->field('id,name,image')->find();
        $result->comment_number = MerchantLog::where('merchant_id', '=', $result->id)->count();
        $type_id = explode(',', $result['business_type_id']);
        if ($type_id) {
            if ($this->language == 1) {
                $typename = Businesstype::whereIn('id', $type_id)
                    ->column('typename');
                $result->business_type_name = implode('/', $typename);
                $merchant_type_name = MerchantType::where('id', '=', $result->merchant_type_id)
                    ->value('name');
            } else {
                $typename = Businesstype::whereIn('id', $type_id)
                    ->column('typename1');
                $result->business_type_name = implode('/', $typename);
                $merchant_type_name = MerchantType::where('id', '=', $result->merchant_type_id)
                    ->value('en_name');
            }
            $result->merchant_type_name = $merchant_type_name;
        }

        if ($result->images) {
            $result->images = explode(',', $result->images);
        } else {
            $result->images = [];
        }
        //浏览+1
        Merchant::where('id', '=', $param['merchant_id'])->setInc('watch_number');
        $this->success('ok', $result);

    }

    /**
     * 商家入驻
     */
    public function createMechant()
    {
        $user_id = $this->auth->id;
        if (!$user_id) {
            $this->error('请先登录！');
        }
        $param = (array)$this->request->post();
        $rule = [
            'name' => 'require',
            'image' => 'require',
            'business_type_id' => 'require',
            'phone' => 'require',
            'merchant_type_id' => 'require|integer',
            'country_id' => 'require|integer',
            'address' => 'require',
            'social_account_type' => 'require|integer|gt:0',
            'social_account' => 'require',
            'describe' => 'require',
//            'main_goods' => 'require',
        ];
        $msg = [
            'name' => '商家名称不能为空',
            'image' => '商家顶部轮播图不能为空',
            'business_type_id' => '商家类型不能为空',
            'phone' => '联系方式不能为空',
            'merchant_type_id' => '支付类型不能为空',
            'country_id' => '地区不能为空',
            'address' => '详细地址不能为空',
            'social_account_type' => '社交账号类型不能为空',
            'social_account' => '社交账号不能为空',
            'describe' => '商家简介不能为空',
//            'main_goods' => '商品详情不能为空',
        ];
        $validate = new \think\Validate($rule, $msg);
        if (!$validate->check($param)) {
            $this->error($validate->getError());
        }
        if (isset($param['image']) && $param['image']) {
            $image = explode(',', $param['image']);
            $param['image'] = $image[0];
            $param['images'] = $param['image'];
        }
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
            $param['province'] = $mapData['province'];
            $param['city'] = $mapData['city'];
            $param['area'] = $mapData['district'];
            // 解析定位信息
            if (isset($mapData['location']) && trim($mapData['location']) != '') {
                $locationAddress = explode(',', trim($mapData['location']));
                $param['lng'] = $locationAddress[0];
                $param['lat'] = $locationAddress[1];
            }
        }
        $param['user_id'] = $user_id;
        $time = time();
        $param['createtime'] = $time;
        $param['updatetime'] = $time;
        $param['status'] = 1;
        unset($param['region']);
        $result = Db::name('merchant')->insertGetId($param);
        $result ? $this->success('入驻成功，请等待审核！') : $this->error('入驻失败！');
    }

}
