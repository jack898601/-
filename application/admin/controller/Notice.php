<?php

namespace app\admin\controller;

use think\console\command\optimize\Route;
use think\Controller;
use think\Db;
use think\Request;
use think\Image;

class   Notice extends Controller
{
    public $url;
    public function index()
    {
        $data = Db::table('notice')->order('id')->select();
        $this->assign('data', $data);
        return $this->fetch('admin/notice');
        
    }
    
    /**
     * 修改内容保存
     *
     * @return \think\Response
     */
    public function update(Request $request)
    {
        $info = $request->get();
        
        Db::table('notice')->where('id', $info['id'])->update(['status'=>$info['show'], 'notice'=>$info['text'], 'noticename'=>$info['noticename']]);
        return $this->index();
    }
    
    /**
     * 封面删除
     *
     * @return \think\Response
     */
    public function detnotice($id)
    {
        Db::table('notice')->where('id',$id)->delete();
    }
    
    /**
     * 增加图片
     *
     * @return \think\Response
     */
    public function getnotice(Request $request)
    {
        // 获取表单上传文件
        $file = request()->file('image');
        //判断是否为空
        if ($file == null)
        {
            return $this->error('无法上传');
        }
        // 移动到框架应用根目录/public/static/index/images/ 目录下,重新命名并建好文件夹
        $info = $file->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'static/index/images');
        if  ($info){
            //完整的图片名加后缀
            $url = $info->getSaveName();
//            var_dump($url);die;
            //后缀
            $ext = $info->getExtension();
//            var_dump($ext);die;
            //图片名
            $str = strstr($url,'.',true);
//            var_dump($str);die;
            // 缩放图片
            $user_img = Image::open('static/index/images/'.$url);
            $user_img->thumb(300, 300)->save('static/index/images/'.$str.'.'.$ext);
            // 执行存入数据库方法
            $this->filename = $url;
            if ($this->save($url) && $this->filename) {
                $this->success('添加成功');
            } else {
                $this->error('新增失败');
            }
        } else{
            //格式不对返回
            $this->error('格式不正确');
        }
    }
    
    /**
     * 图片添加到数据
     *
     * @return \think\Response
     */
    public function save($url)
    {
//        var_dump($url);die;
       return Db::table('notice')
            ->data(['noticeurl'=>$url])
            ->insert();
    }
}
