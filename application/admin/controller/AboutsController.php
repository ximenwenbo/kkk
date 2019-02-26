<?php
namespace app\admin\controller;


use app\admin\model\Tel;
use think\Controller;
use think\Request;
use think\Route;
use app\admin\model\Shouye;

class AboutsController extends Controller
{



    public function xiugai(Request $request,tel $tel){


        if ($request->isPost()){

            $data = $request->post();
           //做更新操作
            $rst = $tel->update($data);  //返回修改记录条数
            if($rst){


                return   ['status'=>'success'];
            }else{
                return  ['status'=>'failure'];
            }

        }else{
            //依赖注入获取对象
            $this->assign('tel',$tel);

            //展示修改表单
            return $this->fetch();
        }



    }

    //前台首页两个模块
    public function shouye(){
        return $this->fetch();
    }


    //首页公司文化和公司业务编辑
    public function edit(Request $request){
        $data = $request->post();
        $data['id'] = 1;
        $shouye = new shouye();

        $rst = $shouye->update($data);

        if($rst){


            return   ['status'=>'success'];
        }else{
            return  ['status'=>'failure'];
        }






    }


    public function index(){

          $info = Shouye::select();
          $info = $info[0];




          $this->assign('info',$info);

          return $this->fetch();

    }



}


















