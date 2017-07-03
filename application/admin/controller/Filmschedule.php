<?php 

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class Filmschedule extends Controller
{
    public function index()
    {
        $list = Db::table(['address,film,schedule'])->field('schedule.id,schedule.price,film.filmname,address.name,schedule.date,schedule.hall_num')->where('schedule.film_id = film.id and schedule.address_id = address.id')->order('id')->paginate(4,false);
        $page = $list->render();
        // 把分页数据赋值给模板变量order
        $this->assign('list', $list);
        $this->assign('page', $page);
        return view('admin/filmschedule',['list'=>$list]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        // 所有电影院的名字
        $addresslist = Db::table('address')->column('name','id');
        $filmlist = Db::table('film')->column('filmname','id');
        $this->assign('addresslist', $addresslist);
        $this->assign('filmlist', $filmlist);
        return $this->fetch('admin/createfilmschedule');
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

        Db::name('schedule')
        ->insert([
            'hall_num' => $data['hallnum'],
            'film_id' => $data['filmname'],
            'address_id' => $data['addressname'],
            'date' => $data['playdate'],
            'price' => $data['price'],
            'playtime' => $data['playtime']
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
        $data = Db::query('select s.id, s.price, f.filmname, a.name, s.date, s.playtime, s.hall_num from schedule s, address a, film f where s.film_id = f.id and s.address_id=a.id and s.id='.$id);
        $addresslist = Db::table('address')->column('name','id');
        $filmlist = Db::table('film')->column('filmname','id');

        return $this->fetch('admin/editfilmschedule',[
                    'addresslist'  => $addresslist,
                    'filmlist' => $filmlist,
                    'data' => $data[0]
                ]);
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
        $res = Db::table('schedule')
        ->where('id', $request->param()['id'])
        ->update([
        'address_id' => $request->param()['addressname'],
        'film_id' => $request->param()['filmname'],
        'date' => $request->param()['playdate'],
        'hall_num' => $request->param()['hallnum'],
        'price' => $request->param()['price'],
        'playtime' => $request->param()['playtime']
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
        $result = Db::table('schedule')->delete($id);
        if ($result > 0 ) {
            $info['status'] = true;
            $info['id'] = $id;
            $info['info'] = 'ID为: ' . $id . ' 的排片表已删除';
        } else {
            $info['status'] = false;
            $info['id'] = $id;
            $info['info'] = 'ID为: ' . $id . ' 的排片表删除失败,请重试!';
        }

        return json($info);
    }

}

