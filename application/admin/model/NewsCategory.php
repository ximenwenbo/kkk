<?php
namespace  app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class NewsCategory extends Model{
    use SoftDelete;

    //控制器方法依赖注入执行的地方
    public static function invoke(\think\Request $request)
    {
        $id = $request->param('category_id');
        return self::find($id);
    }

}