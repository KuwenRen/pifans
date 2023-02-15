<?php

namespace app\admin\controller\pinetwork;

use app\common\controller\Backend;

/**
 * pinetwork新闻类型
 *
 * @icon fa fa-circle-o
 */
class Pinetworktype extends Backend
{

    /**
     * Pinetworktype模型对象
     * @var \app\admin\model\pinetwork\Pinetworktype
     */
    protected $model = null;

    protected $noNeedLogin = ['getTypeList'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\pinetwork\Pinetworktype;

    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    public function getTypeList()
    {
        $param = $this->request->param();
        if (!empty($param['keyValue'])) {
            $ids = explode(',', $param['keyValue']);
            $list = [];
            foreach ($ids as $k => $v) {
                $tt = $this->model->field('id,typename as name')
                    ->where('id', $v)->find();
                array_push($list, $tt);
            }
        } else {
            $list = $this->model->field('id,typename as name')
                ->order('id ASC')
                ->select();
        }
        $result = array("total" => count($list), 'list' => $list);
        return json($result);
    }

    public function getWebTypes()
    {
        $list = $this->model->select();
        $res = [];
        foreach ($list as $k => $v) {
            $res[$v['id']] = $v['typename'];
        }
        return $res;
    }


}
