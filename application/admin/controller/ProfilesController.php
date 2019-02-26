<?php
namespace app\admin\controller;


use app\admin\model\Profiles;
use think\Controller;
use think\Request;
use think\Route;

class ProfilesController extends Controller
{










    public function index(){

          $info = Profiles::select();





          $this->assign('info',$info);

          return $this->fetch();

    }

    public function tianjia(Request $request){
        if ($request->isPost()){

              $data = $request->post();
              $profiles = new Profiles();
              $rst = $profiles->save($data);

              if ($rst){
                  return ['status'=>'success'];
              }else{
                  return ['status'=>'failure'];
              }




        }else{
            return $this->fetch();
        }

    }

    public function xiugai(Request $request,Profiles $profiles){
        if ($request->isPost()){
            $data = $request->post();
            //做更新操作
            $p = new Profiles();
            $rst = $p->update($data);

                if ($rst){
                    return ['status' => 'success'];
                }else{
                    return ['status' => 'failure'];
                }



        }else{
            $this->assign('info',$profiles);
            return $this->fetch();


        }

    }



}


















