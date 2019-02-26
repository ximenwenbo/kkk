<?php
namespace app\admin\controller;


use app\admin\model\Tel;
use think\cache\driver\Redis;
use think\Controller;
use think\Request;
use think\Route;
use think\Db;
use app\admin\model\NewsCategory;



class NewsCategoryController extends Controller
{


    public function  index(){
        $info =  NewsCategory::select();


        $this->assign('info',$info);


        return $this->fetch();
    }

    public function tianjia(Request $request){
        if ($request->isPost()){

          $news = new NewsCategory();
          $data = $request->post();

          if ($data){
              $rst = $news->save($data);
              if ($rst){
                  return ['info'=>1];
              }else{
                  return ['info'=>0];
              }
          }

        }else{
            return $this->fetch();
        }

    }

        /**
         * 修改分类
         */

        public function xiugai(Request $request,NewsCategory $newsCategory){

            if ($request->isPost()){

                $data = $request->post();


                $rst = $newsCategory->update($data);

                if ($rst){
                    return ['info'=>1];
                }else{
                    return ['info'=>0];
                }

            }else{
               $this->assign('category',$newsCategory);
               return $this->fetch();

            }

        }


        public function shanchu(NewsCategory $newsCategory){

          $rst = $newsCategory->delete();
           if ($rst){
             return ['info'=>1];
           }else{
               return ['info'=>0];
           }

}
















}


















