<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class User extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($page=1)
    {
        // 如果没登录,不能访问user首页
        if (!session('name', '', 'admin')){
            $this->redirect('admin/adminlogin/index');
        }
        // 禁用和正常用户数量
        $normal = Db::table('user')->field('count(status)')->where('status = 1')->find()['count(status)'];
        $disable = Db::table('user')->field('count(status)')->where('status = 0')->find()['count(status)'];
        $vip = Db::table('user')->field('count(isvip)')->where('isvip = 1')->find()['count(isvip)'];
        $notvip = Db::table('user')->field('count(isvip)')->where('isvip = 0')->find()['count(isvip)'];

        $list = Db::table('user')->order('id')->paginate(10,false);
        $page = $list->render();
        return $this->fetch('admin/index',[
            'list'=>$list,
            'page'=>$page,
            'vip'=>$vip,
            'notvip'=>$notvip,
            'normal'=>$normal,
            'disable'=>$disable
            ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return $this->fetch('admin/createuser');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 获取传输的数据
        $data = Request::instance()->post();
        if ($data['repass'] != $data['pass']) {
            $info['status'] = false;
            $info['info'] = '两次密码不一致';
            return json($info);
        }

        Db::table('user')->insert([
            'username' => $data['name'],
            'nickname' => $data['nickname'],
            'pass' => md5($data['pass']),
            'phone' => $data['phone'],
            'isvip' => $data['vip']
        ]);
        $info['info'] = '成功';
        return json($info);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $data = Db::table('user')->find($id);
        $this->assign('data', $data);
        return $this->fetch('admin/edit');
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $res = Db::table('user')
        ->where('id', $request->param()['id'])
        ->update([
        'username' => $request->param()['name'],
        'nickname' => $request->param()['nickname'],
        'pass' => $request->param()['pass'],
        'phone' => $request->param()['phone'],
        'isvip' => $request->param()['vip'],
        ]);

        if ($res > 0 ) {
            $info['status'] = true;
            $info['info'] = '更新成功';
        } else {
            $info['status'] = false;
            $info['info'] = '更新失败';
        }
        return json($info);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $result = Db::table('user')->delete($id);
        if ($result > 0 ) {
            $info['status'] = true;
            $info['id'] = $id;
            $info['info'] = 'ID为: ' . $id . ' 的用户已删除';
        } else {
            $info['status'] = false;
            $info['id'] = $id;
            $info['info'] = 'ID为: ' . $id . ' 的用户删除失败,请重试!';
        }

        return json($info);
    }
}
