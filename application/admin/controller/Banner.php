<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Banner extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public $url;
    public function add()
    {
        $data = Db::table('banner')->order('id')->select();
        $this->assign('data', $data);
        return $this->fetch('admin/bannerurl');
    }
    
    /**
     * 封面显示管理
     *
     * @return \think\Response
     */
    public function cancel($id, $content)
    {
        if ($content == '取消封面'){
            Db::table('banner')
                ->where('id', $id)
                ->update(['face' => '2']);
        } elseif($content == '设为封面'){
            Db::table('banner')
                ->where('id', $id)
                ->update(['face' => '1']);
        }
    }
    
    /**
     * 封面删除
     *
     * @return \think\Response
     */
    public function detbanner($id)
    {
        Db::table('banner')->where('id',$id)->delete();
    }
    
    /**
     * 增加图片
     *
     * @return \think\Response
     */
    public function getbanner(Request $request)
    {
        // 获取表单上传文件
        $file = request()->file('image');
        //判断是否为空
        if ($file == null)
        {
           return $this->error('无法上传');
        }
        // 移动到框架应用根目录/public/static/index/images/ 目录下
        $info = $file->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'static/index/images');
        if($info){
            // 成功上传后 获取上传信息
            $url = $info->getSaveName();
            $this->url = $url;
            $this->insert();
            $this->success('增加成功');
        }else{
            // 上传失败获取错误信息
            $this->error('添加失败');
        }
    }
    
    /**
     * 图片添加到数据
     *
     * @return \think\Response
     */
    public function insert()
    {
        Db::table('banner')
            ->data(['face'=>'2','bannerurl'=>$this->url])
            ->insert();
    }

}
