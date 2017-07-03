<?php 

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Image;

class upload extends Controller
{
    private $filename;
    private $id_film;


    /**
     * 当前电影的图片信息
     * @param $id
     * @return 
     */
    public function index($id)
    {
        $this->id_film = $id;
        $data = Db::query('select f.filmname, a.id, a.imgurl, a.face from film f, face a where a.id_film=:id and a.id_film=f.id',['id'=>$id]);
        $this->assign('data', $data);
        $this->assign('id_film', $id);
        return $this->fetch('admin/imgfilm');
    }


    /**
     * 保存上传的图片并缩放 (60*60)
     * @param $request
     * @return 跳转
     */
    public function upload(Request $request)
    {
        // 接受input表单传来的hidden的id
        $this->id_film = $request->only(['id_film'])['id_film'];
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下,重新命名并建好文件夹
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        $url = $info->getSaveName();
        $ext = $info->getExtension();
        $str = strstr($url,'.',true);
        // 缩放图片
        $image = Image::open('uploads/'.$url);
        $image->thumb(60, 60)->save('uploads/'.$str.'x60.'.$ext);
        // 执行存入数据库方法
        $this->filename = $url;
        if ($this->save() && $this->filename) {
            $this->success('添加成功');
        } else {
            $this->error('新增失败');
        }
        
    }
    // 将图片信息存入数据库
    private function save()
    {
        $data = ['id_film' => $this->id_film, 'imgurl' => $this->filename];
        return Db::table('face')->insert($data);
    }

    /**
     * 删除图片信息(数据库和本地)
     * @param $id, $url
     * @return json(ajax)
     */
    public function delete($id, $url)
    {
        // 删除数据库信息
        $result = Db::table('face')->delete($id);

        // 删除本地图片
        // ..
        // is_file($path) && unlink($path);

        if ($result > 0 ) {
            $info['status'] = true;
            $info['id'] = $id;
            $info['info'] = '删除成功!';
        } else {
            $info['status'] = false;
            $info['id'] = $id;
            $info['info'] = '删除失败,请重试/(ㄒoㄒ)/~~';
        }
        return json($info);
    }

}