<?php
/**
 * Created by PhpStorm.
 * User: 何明兴
 * Date: 2022/11/11
 * Time: 17:25
 */

namespace app\api\controller;

use app\common\controller\Api;

/**
 * Class File
 * @package app\api\controller
 * 文件
 */
class File extends Api
{
    protected $noNeedRight = '*';
    protected $noNeedLogin = '*';

    /**
     * 上传图片
     */
    public function uploadAvatar()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            $path = '/uploads_v/';
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads_v');
            if ($info) {
                $filename = config('domain') . $path . $info->getSaveName();
                $this->success('上传成功', $filename);
            } else {
                // 上传失败获取错误信息
                $this->error($file->getError());
            }
        }
    }

    public function uploadImgs()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            $path = '/uploads_v/';
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads_v');
            if ($info) {
                $filename = $path . $info->getSaveName();
                $this->success('上传成功', $filename);
            } else {
                // 上传失败获取错误信息
                $this->error($file->getError());
            }
        }
    }
}
