<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use think\Image;

class User extends Controller
{
    //用户注册
    public function Reg(Request $request)
    {
        $p = $request->post();
        //判断两次密码是否一致
        if ($p['passwd'] !== $p['repwd'])
        {
            return $this->error('两次密码不一致');
        }
        //处理数据
        $data = [
            'username' => $p['user'],
            'pass' => md5($p['passwd']),
            'phone' => $p['iphone'],
            'regtime' => time(),
        ];
        // 判断用户名 和 电话 是否存在
        $m = Db::table('user')->field('username, phone')->select();
            foreach ($m as $v)
            {
                if ($data['username'] == $v['username'] ){
                    return $this->error('用户名已存在');
                }
                if ($data['phone'] == $v['phone']){
                    return $this->error('该手机号已注册');
                }
            }
        //执行增加语句
        Db::name('user')->data($data)->insert();
        $this->success('注册成功，请登录', 'index/index/login');
    }
    
    //用户登录跳转
    public function index()
    {
        //判断session是否有值
        if (Session::get('name', 'index') != null){
            return  $this->redirect('index/index/index');
        }
        //session无值直接就跳转首页
        return  $this->redirect('index/index/index');
    }
    
    //用户退出管理
    public function loginout()
    {
        //退出直接情况session
        session(null, 'index');
       return $this->index();
    }
    
    //用户登陆
    public function login()
    {
        if (empty($_POST['username']) || empty($_POST['p']))
        {
            return $this->error('不能为空');
        }
        $user = $_POST['username'];
        $pwd = $_POST['p'];
        $name = Db::table('user')->where('username', $user)->field('username, pass, id')->select();
        foreach ($name as $k => $v){
            if ($user != $v['username'] || md5($pwd) != $v['pass']){
                return $this->error('密码不正确');
            }
            Session::set('name', $user, 'index');
            Session::set('user_id', $name[0]['id'], 'index');
            return $this->index();
        }
        return $this->error('密码不正确');
    }


    //修改个人信息
    public function update(Request $request)
    {
        //接收前台个人中心传过来的修改数据
        $info = $request->put();
        $data = [
            'email' => $info['email'],
            'nickname' => $info['nickname'],
        ];
        $name = $info['username'];
        //根据用户名查找该用户的id
        $id = Db::table('user')->where('username', $name)->field('id')->select();
        //执行更新
        $result = Db::table('user')->where('id',$id[0]['id'])->update($data);
        //判断执行情况
        if ($result > 0) {
            return $this->success('编辑成功');
        } else {
            return $this->error('编辑失败(ノ°ο°)ノ高能预警!');
        }
    }
    
    //修改个人头像
    public function personalimg(Request $request)
    {
        // 接受input表单传来的hidden的id
        $user_id = $this->user_id = $request->only(['user_id'])['user_id'];
        // 获取表单上传文件
        $file = request()->file('user_img');
        //判断是否为空
        if ($file == null)
        {
            return $this->error('无图片');
        }
        // 移动到框架应用根目录/public/static/index/images/ 目录下,重新命名并建好文件夹
        $info = $file->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'static/index/images');
        if  ($info){
            //完整的图片名加后缀
            $url = $info->getSaveName();
            //后缀
            $ext = $info->getExtension();
            //图片名
            $str = strstr($url,'.',true);
            // 缩放图片
            $user_img = Image::open('static/index/images/'.$url);
            $user_img->thumb(55, 55)->save('static/index/images/'.$str.'x55.'.$ext);
            // 执行存入数据库方法
            $this->filename = $url;
            if ($this->save($user_id) && $this->filename) {
                $this->success('添加成功');
            } else {
                $this->error('新增失败');
            }
        } else{
            //格式不对返回
            $this->error('格式不正确');
        }
    }
    
    // 将图片信息存入数据库
    private function save($id)
    {
        $data = ['icon' => $this->filename];
        return Db::table('user')->where('id', $id)->update($data);
    }
    
    //修改个人密码
    public function updatepwd(Request $request)
    {
        //接收前台传输来的数据
        $p = $request->put();
        //判断接收数据是否为空
        if ($p['old_pwd'] == null && $p['new_pwd'] == null){
            return $this->error('无数据更新');
        }
        $data = [
            'pass' => md5($p['new_pwd']),
        ];
        //判断两次密码是否一致
        if (!($p['new_pwd'] == $p['repwd'])){
            return $this->error('两次密码不一致');
        }
        //根据id查找用户的密码
        $adminp = Db::table('user')->where('id', $p['id'])->field('pass')->select();
        //判断密码和数据库密码是否一致
        if (!(md5($p['old_pwd']) == $adminp[0]['pass'])){

            return $this->error('密码错误');
        }
        //如果密码一致则修改密码
        $up = Db::table('user')->where('id', $p['id'])->update($data);
        //判断是否执行成功
//        if ($up > 0) {
//            return $this->success('更改成功');
//        } else {
//            return $this->error('修改失败');
//        }
        return $this->success('更新成功');
    }
    
    //订单管理
    public function charts()
    {
        //接收session里面的用户id
        $user_id = Session::get('user_id','index');
        //头像
        $user = session('name', '','index');
        $result = Db::table('user')->where('username', 'EQ', $user)->field('icon')->select();
        $this->assign('result', $result[0]);
        //分页
        $order = Db::table(['orders,address,film,schedule'])->field('orders.id,schedule.playtime,schedule.date,schedule.hall_num,film.filmname,orders.ticket_number,address.name,address.address,orders.seatnum,orders.status,schedule.price')->where('orders.schedule_id = schedule.id and schedule.address_id = address.id and schedule.film_id = film.id and orders.user_id ='.$user_id)->order('orders.id')->paginate(4, false);
        $page = $order->render();
        // 把分页数据赋值给模板变量order
        $this->assign('order', $order);
        $this->assign('page', $page);
        return $this->fetch('index/charts');

    }
    
    //删除订单
    public function rm_order()
    {
        //获取该订单的id
         $id = $_GET['id'];
         //执行删除
        Db::table('orders')->where('id',$id)->delete();
        return $this->success('删除成功');
    }
    
    //    //收藏页面
    //    public function liked()
    //    {
    //        if($userInfo=Session::get('user')){
    //            $likeWhere=['userid'=>$userInfo['userId']];
    //            $imgData=[];
    //            $fileId=[];
    //            if($likedData=Db::table('shops_liked')->order('id desc')->where($likeWhere)->select()){
    //                foreach($likedData as $k=>$vo){
    //                    $fileId[]=$vo;
    //                    $imgData[$k]=Db::table('')->where('id',$vo['imgid'])->find();
    //                    $imgData[$k]['likedId']=$vo['id'];
    //                };
    //                $this->assign('likedCount',count($fileId));
    //            }
    //            $this->assign('imgData',$imgData);
    //            return $this->fetch();
    //        }else{
    //            header("Location:/index/login");exit;
    //        }
    //    }
}
