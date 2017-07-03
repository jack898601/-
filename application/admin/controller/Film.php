<?php 

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class Film extends Controller
{
    public function index()
    {
        $data = Db::table('film')->order('id')->paginate(10, false);
        $page = $data->render();
        $this->assign('list', $data);
        $this->assign('page', $page);
        return view('admin/film');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return $this->fetch('admin/createfilm');
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

        Db::name('film')
        ->insert([
            'filmname' => $data['name'],
            'language' => $data['language'],
            'duration' => $data['duration'],
            'actor' => $data['actor'],
            'type' => $data['type'],
            'regions' => $data['regions'],
            'release_time' => $data['release_time']
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
        
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $data = Db::table('film')->find($id);
        $this->assign('data', $data);
        return $this->fetch('admin/editfilm');
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
        $res = Db::table('film')
        ->where('id', $request->param()['id'])
        ->update([
        'filmname' => $request->param()['name'],
        'language' => $request->param()['language'],
        'type' => $request->param()['type'],
        'duration' => $request->param()['duration'],
        'release_time' => $request->param()['release_time'],
        'actor' => $request->param()['actor']
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
        $result = Db::table('film')->delete($id);
        if ($result > 0 ) {
            $info['status'] = true;
            $info['id'] = $id;
            $info['info'] = 'ID为: ' . $id . ' 的电影已删除';
        } else {
            $info['status'] = false;
            $info['id'] = $id;
            $info['info'] = 'ID为: ' . $id . ' 的电影删除失败,请重试!';
        }

        return json($info);
    }
}


