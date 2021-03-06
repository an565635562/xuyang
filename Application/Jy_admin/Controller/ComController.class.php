<?php
/***
*  公共
****/
namespace Jy_admin\Controller;
use Think\Controller;
defined('THINK_PATH') or exit('Access Defined!');
class ComController extends Controller {
    protected  $page;               //页码
    protected  $num;                //页数
    protected  $userInfo;           //用户信息
    protected  $lowerAdmingroup;    //我的下级组
    protected  $lowerAdminUser;     //我的下级用户
    public     $upAdmingroup;
    public function __construct(){
           //
           parent::__construct();
           $userInfo  = cookie('userInfo');
//           var_dump($userInfo);die;
           $addr =  CONTROLLER_NAME.ACTION_NAME;

           //后台资源
           $responseAdmin = C('RESOURCES_ADMIN');

          //控制器
           $Controller = array(
               'Loginindex',
               'Loginsign',
               'Loginverification',
               'Logincode',
               'Loginsignout'
           );


            $response = in_array($addr,$Controller);
            $lowerAdmingroup = array();
            $lowerAdminUser = array();
           if(empty($userInfo) &&  !$response) {
               header('Location:/jy_admin/login/index');
               die;
           }elseif(!$response){
               //解密cookie
                $AesKey = C('AesKey');
                $obj = new \Common\Lib\func();

                $Decrypt  =  $obj->Decrypt(base64_decode($userInfo),$AesKey);
                if(!$Decrypt){
                    cookie('userInfo',null);
                    header('Location:/jy_admin/login/index');
                    die;
                }
                $userInfo = json_decode(base64_decode($Decrypt),true);
                if(empty($userInfo['id'])){
                    cookie('userInfo',null);
                    header('Location:/jy_admin/login/index');
                    die;
                }
                $page = I('param.page','','trim');
                $num  = I('param.num','','trim');
                $this->page  = !empty($page) ?  $page-1 : C('ADMIN_PAGE');
                $this->num   = !empty($num)  ?  $num  : C('ADMIN_NUM');
                $adminGroup = M('jy_admin_group')
                   ->field('id,upid,addId')
                   ->select();

                $lowerAdmingroup = array();
                $lowerAdminUser  = array();
               if($userInfo['default'] == 1){
                   $admingroup =  $userInfo['admingroup'];
                   //我的下级组
                   $lowerAdmingroup = $this->make_tree($adminGroup,'id','upid','',$admingroup);
                   //我的下级组员
                   $lowerAdminUser = array();
                   $adminUser = M('jy_admin_users')
                                ->field('id,admingroup')
                                ->select();



                   foreach ($adminUser as $k=>$v){
                       if(in_array($v['admingroup'],$lowerAdmingroup)){
                           $lowerAdminUser[] = $v['id'];
                       }
                   }
               }else{
                   //读写权限
                   $userInfo['add']   = 2;
                   $userInfo['del']   = 2;
                   $userInfo['edit']  = 2;
               }

               $this->userInfo        = $userInfo;
               $this->lowerAdmingroup = $lowerAdmingroup;
               $this->lowerAdminUser  = $lowerAdminUser;

           }

          $this->assign('responseAdmin',$responseAdmin);
    }
    /***
     *  无限极递菜单
     * @param  array    $list        全部菜单
     * @param  int      $pk          菜单ID
     * @param  int      $upid        父级id
     * @param  string   $sub_menu    子菜单名
     * @param  int      $root        最上级upid
     * return  array    返回结果集
     ***********/
    public function make_tree($list,$pk='id',$upid='upid',$sub_menu ='sub_menu',$root=0){
        $tree=array();
        foreach($list as $k=>$v){

            if($v[$upid] == $root){
                unset($list[$k]);
                if(!empty($list)){
                    $child= $this->make_tree($list,$pk,$upid,$sub_menu,$v[$pk]);
                    if(!empty($child)){
                        foreach($child as $key=>$val){
                            $tree[] = $val['id'];
                        }
                    }
                }
                $tree[]=$v['id'];
            }


        }
        return $tree;
    }
}