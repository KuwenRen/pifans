<?php
/**
 * Created by PhpStorm.
 * User: 何明兴
 * Date: 2022/11/10
 * Time: 17:13
 */

namespace app\api\controller;

use app\admin\model\pinetwork\PinetworkList;
use app\admin\model\pinetwork\Pinetworktype;
use app\common\controller\Api;

/**
 * Class Web
 * @package app\api\controller
 * web
 */
class Pinetwork extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 获取web3类型
     */
    public function typelist()
    {
        $result = PinetworkType::where('id', '>', 0)
            ->order('weigh', 'asc')
            ->where('status', '=', 1)
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
     * 获取web3内容
     */
    public function getPinetworkList()
    {
        $params = $this->request->param();
        $page = isset($params['page']) ? $params['page'] : 1;
        $limit = isset($params['limit']) ? $params['limit'] : 10;
        $result = new PinetworkList();
        $where = [];
        if (isset($params['type_id']) && $params['type_id']) {
            $where['type_id'] = ['=', $params['type_id']];
        }
        if (isset($params['keyword']) && $params['keyword']) {
            $where['title'] = ['like', '%' . $params['keyword'] . '%'];
        }
        $totals = $result->where('status_switch', '=', 1)
            ->where($where)->count();
        $list = $result
            ->where($where)
            ->where('status_switch', '=', 1)
            ->limit(($page - 1) * $limit, $limit)
            ->field('id,title as title1,title1 as title2,show_image as image,create_time')
            ->order('weigh', 'asc')
            ->select();
        if ($list) {
            foreach ($list as $k => $v) {
                $list[$k]['createtime'] = $v['create_time'];
                $list[$k]['createtime'] = strtotime($v['create_time']);
            }
        }
        $pages = ceil($totals / $limit);
        $this->success('ok', compact('list', 'totals', 'pages'));
    }
}
