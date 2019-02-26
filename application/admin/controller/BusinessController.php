<?php
namespace app\admin\controller;



use app\admin\model\Business;
use think\Controller;
use think\Request;


class BusinessController extends  Controller{

    public function index(){

        //获得主营业务
        $business = Business::select();


        $this->assign('info',$business);

        return $this->fetch();
    }

    public function tianjia(Request $request){
        if ($request->isPost()){

            //接收表单信息
            $data = $request->post();
            //实例化业务类
           $business = new Business();
           //做添加入库
           $rst = $business->save($data);
            if($rst){


                return   ['status'=>'success'];
            }else{
                return  ['status'=>'failure'];
            }


        }else{
           return $this->fetch();
        }

    }


    //修改业务
    public function xiugai(Request $request,Business $business)
    {
        if ($request->isPost()){

            $data = $request->post();
            $rst = $business->update($data);

            if($rst){


                return   ['info'=>1];
            }else{
                return  ['info'=>0];
            }

        }else{

           $this->assign('info',$business);
            return $this->fetch();
        }
    }


    //删除业务
    public function shanchu(Business $business){
        $result  =  $business->delete();
        if($result){


            return   ['status'=>'success'];
        }else{
            return  ['status'=>'failure'];
        }


    }
}