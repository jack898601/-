<?php 

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;

class Adminlogin extends Controller
{
    public function index()
    {
        // 如果已经登录,则跳转首页
        if (Session::get('name','admin')){
            $this->redirect('admin/user/index');
        }
        return view('admin/login');
    }

    // 后台管理登录
    public function Login(Request $request)
    {
        $name = $request->param()['name'];
        $pass =  $request->param()['pass'];

        if ($name && $pass) {
            // 验证数据库
            $rst = Db::table('admin')->where('name', $name)->where('pass', $pass)->find();
            if ($rst) {
                // 查到了就存入session
                session('name', $name, 'admin');
                session('pass', $pass, 'admin');
                $this->success('登录成功', '/admin');
            } else {
                // 用户名或者密码错误,请重试
                $this->error('用户名或者密码错误,请重试!');
            }
        } else {
            // 密码或名字不能为空
            $this->error('密码或名字不能为空,请重试!');
        }
    }

    // 退出登录
    public function out()
    {
        session(null, 'admin');
        $this->success('安全登出', '/adlog');
    }
}


