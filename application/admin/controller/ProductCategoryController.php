<?php
namespace app\admin\controller;



use app\admin\model\ProductCategory;
use think\Controller;
use think\Request;


class ProductCategoryController extends  Controller{

    public function index(){

        /**
         * @param 获得团队列表
         */

        $teams = ProductCategory::select();

        $this->assign('info',$teams);





        return $this->fetch();
    }

    /**
     * @param Request $request
     * @return   添加团队成员
     */
    public function tianjia(Request $request){
        if ($request->isPost()){
            $data = $request->post();
          $product = new ProductCategory();
          $rst  = $product->save($data);









            if ($rst){
                return ['info'=>1];
            }else{
                return  ['info'=>0];
            }

        }else{
            return $this->fetch();
        }

    }

    /**
     *   修改团队成员
     */
    public function xiugai(Request $request,ProductCategory $productCategory){


        if ($request->isPost()){
            /**
             * 修改入库
             */

            $infos = $request->post();



         $result = $productCategory->Update($infos);





            if ($result){

                return ['info'=>1];
            }else{

                return ['info'=>0];
            }


        }else{




            $this->assign('info',$productCategory);





            return $this->fetch();
        }



    }


    public function pics_up(Request $request)
    {
        //接收客户端传递过来的附件，并存储到服务器上
        //$request调用file()方法就可以获得被上传附件
        //以"think\File"类对象形式返回
        $file = $request -> file('mypics');
        //dump($file);  //think\File类对象

        $path = "./uploads/picstmp/";  //图片存储目录

        //图片上传,move()方法执行成功会返回think\File类对象
        //       上传失败会返回false信息
        //think\File 内部通过算法会给每个上传图片定义一个唯一名字
        $result = $file -> move($path);
        if($result){
            //获得上传好的图片信息
            //获得上传好图片路径名信息
            $filename = $result->getSaveName(); //20160820\42a79759f284b767dfcb2a0197904287.jpg

            $pathfilename = $path.$filename; //拼装图片完整路径名
            $pathfilename = str_replace('\\','/',$pathfilename);//"\"替换为"/"

            return ['status'=>'success','pathfilename'=>$pathfilename];
        }else{
            //上传图片失败
            $errorinfo = $file -> getError();
            return ['status'=>'failure','errorinfo'=>$errorinfo];
        }
    }


    /**
     *   删除成员
     */

    public function shanchu(ProductCategory $productCategory){

        $rst = $productCategory->delete();

        if ($rst){

            return ['info'=>1];
        }else{
            return ['info'=>0];
        }


    }

}