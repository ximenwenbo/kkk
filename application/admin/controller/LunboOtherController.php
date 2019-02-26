<?php
namespace app\admin\controller;


use app\admin\model\LunboOther;
use think\Controller;
use think\Request;
use think\Validate;



class LunboOtherController extends Controller
{







    /**
     * 给商品做logo图片上传处理 [post]
     * @param Request $request
     */
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

    public function index(){

         $infos = LunboOther::select();

         $this->assign('infos',$infos);

         return $this->fetch();



    }

    /**
     * 轮播图第一张修改
     */

    public function xiugai(Request $request,LunboOther $lunboOther){


        if ($request->isPost()){
            /**
             * 接收传递过来的图片路径信息
             */

            $infos = $request->post();


            /***商品logo图片修改维护01***/
            if(strpos($infos['img_path'],'picstmp')!==false){

                //① 判断有上传新banner图片才维护
                //② 删除当前对应的旧图片(删除物理图片)
                if(!empty($lunboOther->img_path) && file_exists($lunboOther->img_path)){
                    unlink($lunboOther->img_path);
                }

                //③ 创建"年月日"的文件目录
                $path = './uploads/pics/'.date('Ymd');
                if(!file_exists($path)){
                    mkdir($path,0777,true);
                }
                //制作图片终极路径名
                $finalPathName = str_replace('picstmp','pics',$infos['img_path']);
                //图片从临时位置 挪到终极位置
                rename($infos['img_path'],$finalPathName);
                //设置 终极图片路径名 存储到数据库中
                $infos['img_path'] = $finalPathName;

            }elseif(empty($infos['img_path']) && !empty($lunboOther->img_path)){
                //B. 清除商品原有的旧图片
                if(file_exists($lunboOther->img_path)){
                    unlink($lunboOther->img_path);
                }
            }else{
                //C. 保持原有logo图片不变(不要修改)
                unset($infos['img_path']);
            }
            $result = $lunboOther->update($infos);
            if ($result){

                return ['info'=>1];
            }else{

                return ['info'=>0];
            }


        }else{
            $this->assign('info',$lunboOther);





            return $this->fetch();
        }



    }

    public function tianjia(Request $request){
        if ($request->isPost()){


            $infos = $request->post();
            //通过程序创建"年月日"的子级目录
            $path = "./uploads/pics/".date('Ymd');
            //判断目录不存在
            if(!file_exists($path)){
                mkdir($path,0777,true);
            }
            //设置图片的"终极"存储目录路径名
            //./uploads/picstmp/20181129/0c4c6b67dd2b03a3e106334e83373ac8.jpg [临时的]
            //./uploads/pics/20181129/0c4c6b67dd2b03a3e106334e83373ac8.jpg [终极的]
            $finalPathName = str_replace('picstmp','pics',$infos['img_path']);
            //把图片从“临时”位置挪到“终极”存储位置
            rename($infos['img_path'],$finalPathName);
            $infos['img_path'] = $finalPathName;  //终极路径名要存储到数据库中去
            $lunboother = new LunboOther();
            $rst = $lunboother ->save($infos);

            if ($rst){
                return ['info'=>1];
            }else{
                return  ['info'=>0];
            }

        }else{
            return $this->fetch();
        }

    }

    public function shanchu(LunboOther $lunboOther){

       $rst = $lunboOther->delete();
       if ($rst){
           return ['info'=>1];
       }else{
           return ['info'=>0];
       }

    }


/**
 * @Param  banner图上线与下线
 */

    public function change_status(Request $request){
        $info = $request->post(); // ['id'=>5,'is_use'=>0];
       $rst  = LunboOther::update($info);

       if ($rst){
           return ['info'=>1];
       }else{
           return ['info'=>0];
       }


    }



}


















