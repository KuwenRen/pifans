<?php

namespace app\admin\controller\chat;

use app\common\controller\Backend;
use function Sodium\compare;

/**
 * 聊天室
 *
 * @icon fa fa-circle-o
 */
class Groupchat extends Backend
{

    /**
     * Groupchat模型对象
     * @var \app\admin\model\chat\Groupchat
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\chat\Groupchat;
        $this->view->assign("statusList", $this->model->getStatusList());
        $typelist = (new Chattype())->getChatTypes();
        $this->assignconfig("typeList", $typelist);
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

    public function chooseYesOrNot()
    {
        $list = [['id' => 0, 'name' => '否'], ['id' => 1, 'name' => '是']];
        $request = $this->request->post();
        if (isset($request['keyValue'])) {
            $result = array("total" => 1, 'list' => $list[$request['keyValue']]);
        } else {
            $result = array("total" => count($list), 'list' => $list);
        }
        return json($result);
    }

}
