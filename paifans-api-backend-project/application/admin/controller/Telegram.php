<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 电报
 *
 * @icon fa fa-circle-o
 */
class Telegram extends Backend
{
    
    /**
     * Telegram模型对象
     * @var \app\admin\model\Telegram
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Telegram;
        $this->view->assign("isHotList", $this->model->getIsHotList());

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
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['country'])
                    ->where($where)
                    ->order('weigh desc,id desc')
                    ->paginate($limit);

            foreach ($list as $row) {
                
                $row->getRelation('country')->visible(['name1','image']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

}
