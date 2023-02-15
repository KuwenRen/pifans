<?php
/**
 * Created by PhpStorm.
 * User: 何明兴
 * Date: 2022/11/10
 * Time: 17:13
 */

namespace app\api\controller;

use app\admin\model\pinetwork\PinetworkList;
use app\admin\model\web\Weblist;
use app\admin\model\web\Webtype;
use app\common\controller\Api;
use think\Db;

/**
 * Class Web
 * @package app\api\controller
 * web
 */
class Web extends Api
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

        $result = WebType::where('id', '>', 0)
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
     * 获取web3内容
     */
    public function getWebList()
    {
        $params = $this->request->post();
        $page = isset($params['page']) ? $params['page'] : 1;
        $limit = isset($params['limit']) ? $params['limit'] : 10;
        $result = new Weblist();
        $data = $result;
        if (isset($params['type_id']) && (int)$params['type_id']) {
            $data = $result->where('type_id', '=', (int)$params['type_id']);
        }
        if (isset($params['keyword']) && $params['keyword']) {
            $data = $result->whereLike('title', '%' . $params['keyword'] . '%');
        }
        $list = $data
            ->limit(($page - 1) * $limit, $limit)
            ->order('weigh', 'asc')
            ->select();
        $totals = 0;
        if (count($list)) {
            foreach ($list as $k => $v) {
                if ($this->language == 2) {
                    $list[$k]['typename'] = Db::name('web_type')
                        ->where('id', '=', $v['type_id'])->value('typename1');
                    $list[$k]['title'] = $v['title1'];
                } else {
                    $list[$k]['typename'] = Db::name('web_type')
                        ->where('id', '=', $v['type_id'])->value('typename');
                }
                unset($list[$k]['title1']);
            }
            $totals = count($list);
        }
        $pages = ceil($totals / $limit);
        $this->success('ok', compact('list', 'totals', 'pages'));
    }

    /**
     * 获取web/pinetwork详情
     */
    public function getNewsDetail()
    {
        $params = $this->request->post();
        $type = isset($params['type']) ? (int)$params['type'] : 0;
        if (!in_array($type, [0, 1])) {
            $this->error('查看类型有误');
        }
        if ($type == 0) {
            $result = new Weblist();
        } else {
            $result = new PinetworkList();
        }
        $data = $result->where('id', '=', $params['id'])
            ->field('id,title,title1,content,content1,create_time,update_time')
            ->find();
        if ($this->language == 2) {
            $data['title'] = $data['title1'];
            $data['content'] = $data['content1'];
        }
        unset($data['title1']);
        unset($data['content1']);
        $this->success('查询成功！', $data);
    }
}
