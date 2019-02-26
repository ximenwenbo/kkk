<?php
namespace app\admin\controller;


use app\admin\model\NewsCategory;
use app\admin\model\Tel;
use think\Controller;
use think\Request;
use think\Route;
use app\admin\model\News;
use think\Validate;
use think\Db;

class NewsController extends Controller
{

    //新闻列表
    public function index(){

        $info  = News::select();
        $this->assign('info',$info);

        return $this->fetch();

    }

    public function ind(Request $request)
    {
        //获得订单列表信息，传递给模板展示
        //制作分页数据
        $orderdata = Order::with('user')
            ->order('order_id desc')
            ->paginate(6);



        //获得分页页码列表信息
        $pagelist = $orderdata->render();

        $this -> assign('orderdata',$orderdata);
        $this -> assign('pagelist',$pagelist);

        return $this -> fetch();
    }
    //添加新闻

    public function tianjia(Request $request){
        if ($request->isPost()){
            //对form表单收集的数据实现校验
            //① 制作验证规则
            $rules = [
                //'表单域name的名称'=>规则,
                //被校验信息 必填，并且News表中title字段没有出现过被验证的内容信息,长度不小于2个字符
                'title'    => 'require|unique:News|min:2',


                //作者必填
                'author'   => 'require',
                //内容必填
                'content'  => 'require',
                 //新闻关键字
                'keywords' => 'require'
             ];

            //② 制作验证的错误提示信息
            $notices = [
                //表单域name的名称.规则 => 具体错误提示,
                'title'    =>  '标题必须填写且长度不能小于两个字符',
                'author'     =>  '作者名称必填',
                'content'        =>  '新闻内容必填',
                'keywords'   =>  '关键字必填',

            ];

            //③ 实例化Validate类对象
            $validate = new Validate($rules,$notices);
          $data = $request->post();


            if($validate ->batch()-> check($data)){
                $news = new News();
                //通过程序创建"年月日"的子级目录
                $path = "./uploads/pics/".date('Ymd');
                //判断目录不存在
                if(!file_exists($path)){
                    mkdir($path,0777,true);
                }
                //设置图片的"终极"存储目录路径名
                //./uploads/picstmp/20181129/0c4c6b67dd2b03a3e106334e83373ac8.jpg [临时的]
                //./uploads/pics/20181129/0c4c6b67dd2b03a3e106334e83373ac8.jpg [终极的]
                $finalPathName = str_replace('picstmp','pics',$data['img']);
                //把图片从“临时”位置挪到“终极”存储位置
                rename($data['img'],$finalPathName);
                $data['img'] = $finalPathName;  //终极路径名要存储到数据库中去
                //添加入库
                $rst = $news->save($data);

                if ($rst){
                    return ['info'=>1];
                }else{
                    return ['info'=>0];
                }

            }else{
                //验证失败
                //获得验证的错误信息
                $errorinfo = $validate->getError();
                //dump($errorinfo);  //一维数组方式返回全部校验错误信息 [goods_name=>xx,goods_price=>yy]
                $errorinfo = implode(',',$errorinfo); //把错误信息由Array变为,号分隔的字符串String
                //xx,yy,zz 等错误提示
                //传递错误信息给客户端显示
                return ['status'=>'failure','errorinfo'=>$errorinfo];
            }

        }else{

            $data = NewsCategory::select();

            $this->assign('data',$data);
            return $this->fetch();
        }
    }


    public function xiugai(Request $request,News $news){
        if ($request->isPost()){

            $data = $request->post();

            /***新闻logo图片修改维护01***/
            if(strpos($data['img'],'picstmp')!==false){

                //① 判断有上传新闻图片才维护
                //② 删除当前对应的旧图片(删除物理图片)
                if(!empty($news->img) && file_exists($news->img)){
                    unlink($news->img);
                }

                //③ 创建"年月日"的文件目录
                $path = './uploads/pics/'.date('Ymd');
                if(!file_exists($path)){
                    mkdir($path,0777,true);
                }
                //制作图片终极路径名
                $finalPathName = str_replace('picstmp','pics',$data['img']);
                //图片从临时位置 挪到终极位置
                rename($data['img'],$finalPathName);
                //设置 终极图片路径名 存储到数据库中
                $data['img'] = $finalPathName;

            }elseif(empty($data['img']) && !empty($news->img)){
                //B. 清除商品原有的旧图片
                if(file_exists($news->img)){
                    unlink($news->img);
                }
            }else{
                //C. 保持原有logo图片不变(不要修改)
                unset($data['img']);
            }
            $result = $news->update($data);
            if ($result){
                return ['info'=>1];
            }else{
                return ['info'=>0];
            }

        }else{
            $this->assign('info',$news);


            $data = NewsCategory::select();

            $this->assign('data',$data);
            return $this->fetch();

        }

    }

    public function shanchu(News $news){
        //逻辑删除 假删除
        $rst = $news->delete();

        if ($rst){
            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }

    }


    public function delsel($ids){

        $news = new News();
        $rst = $news->where('id','in',$ids)->delete();

        if ($rst){
            return ['info'=>1];
        }else{
            return ['info'=>0];

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









}


















