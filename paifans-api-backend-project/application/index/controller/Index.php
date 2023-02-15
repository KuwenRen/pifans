<?php

namespace app\index\controller;

use app\common\controller\Frontend;

class Index extends Frontend
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index()
    {
        if(request()->isMobile()){
            // echo "敬请期待！";
            // die;
            header("Location: https://pifans.app/m");
        } else {
            // echo "敬请期待！";
            // die;
            header("Location: https://pifans.app/web");
        }
        // return $this->view->fetch();
    }

}
