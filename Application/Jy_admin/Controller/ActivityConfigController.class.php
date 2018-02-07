<?php
/****
*   新活动
**/
namespace Jy_admin\Controller;
use Think\Controller;
use Think\Model;

class ActivityConfigController extends ComController {
    public function index(){
        $page           = $this->page;              //页码
        $num            = $this->num;               //条数
        //渠道号
        $search['Status']  = I('param.Status',0,'intval');
        $search['Channel'] = I('param.Channel',0,'intval');
        $search['Type']    = I('param.Type',0,'intval');
        $where = '1';
        if($search['Channel'] != 0 ){
            $where  .= '  and   b.id  = '.$search['Channel'];
        }
        if( $search['Status'] != 0 ){
            $where  .= '  and   a.ConfStatus  ='.$search['Status'];
        }
        if( $search['Type'] != 0 ){
            $where  .= '  and   a.Style  ='.$search['Type'];
        }
        $catChannelField = array(
            'name',
            'account',
            'id',
        );
        $catChannel = M('jy_admin_users')
            ->where('channel = 2')
            ->field($catChannelField)
            ->select();
        $count  = M('conf_activity_father as a')
            ->where($where)
            ->count();
        $Page       = new \Common\Lib\Page($count,$num);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        $activityFatherlistFile = array(
            'a.Id',
            'a.ConfStatus',
            'a.ShowStartTime',
            'a.ShowStartTime',
            'a.AbroadTitle',
            'a.Style',
            'a.Channel',
            'a.Aname',
            'a.Aid',
            'a.DateTime',
            'a.ShowEndTime',
        );
        //查询子活动
        $catSon = M('conf_activity_son')->field(array(
            'Id',
            'FatherID',
            'SonTitle',
        ))->order('Sort asc')
        ->select();

        $info = M('conf_activity_father as a')
            ->where($where)
            ->limit($page*$num,$num)
            ->field($activityFatherlistFile)
            ->select();



        foreach ($info as $k=>$v){
            $dataSon = array();
            $dataSonName = array();
            foreach ($catSon as $key=>$val){
                if($v['Id'] == $val['FatherID']){
                    $dataSon['data'][] = $val;
                    $dataSonName[] = $val['SonTitle'];
                }
            }
            $info[$k]['dataSon']    = $dataSon;
            $info[$k]['dataSonName'] = implode('、',$dataSonName);


            foreach ($catChannel as $key=>$val){
                if($val['account'] == $v['Channel']){
                    $info[$k]['name'] = $val['name'];
                }
            }

            if($v['Channel'] == 'global'){
                $info[$k]['name'] = '全渠道';
            }

        }
        $this->assign('page',$show);
        $this->assign('catChannel',$catChannel);
        $this->assign('info',$info);
        $this->assign('search',$search);
        $this->display('index');
    }
    //添加
    public  function add(){
        $model = new Model;
        $Obj = new \Common\Lib\func();
        $UsersInfo = $this->userInfo;
        $catChannelField = array(
            'name',
            'account',
            'id',
        );
        $catChannel = $model->table('jy_admin_users')
            ->where('channel = 2')
            ->field($catChannelField)
            ->select();
        if(IS_POST){
             $AbroadTitle   = I('param.AbroadTitle','','trim');
             $WithinTitle  = I('param.WithinTitle','','trim');
             $ShowStartTime = I('param.ShowStartTime','','trim');
             $ShowEndTime   = I('param.ShowEndTime','','trim');
             $Style         = I('param.Style',1,'intval');
             $Hot           = I('param.Hot',1,'intval');
             $Sort          = I('param.Sort',0,'intval');
             $Channel       = I('param.Channel','','trim');
             $ConfStatus    = I('param.ConfStatus',1,'intval');
             //判断是否复制
             $IsCp          = I('param.IsCp',1,'intval');
             $CpChannel     = I('param.CpChannel','','trim');
             if($Channel == 'global'){
                 $CpChannel = '';
                 $IsCp      = 1;
             }
             if($IsCp == 1){
                 $CpChannel = '';
             }


             if($ShowStartTime ==''){
                 $ShowStartTime =  date('Y-m-d H',time());
             }
            if($ShowEndTime ==''){
                $ShowEndTime = date('Y-m-d H',time());
            }

             $DataActivityFather = array(
                 'AbroadTitle'     =>  $AbroadTitle,
                 'ShowStartTime'   =>  $ShowStartTime,
                 'ShowEndTime'     =>  $ShowEndTime,
                 'Style'           =>  $Style,
                 'Hot'             =>  $Hot,
                 'Channel'         =>  $Channel,
                 'ConfStatus'      =>  $ConfStatus,
                 'WithinTitle'     =>  $WithinTitle,
                 'Aname'           =>  $UsersInfo['name'],
                 'Sort'            =>  $Sort,
                 'CpChannel'       =>  $CpChannel,
                 'IsCp'            =>  $IsCp,
                 'Aid'             =>  $UsersInfo['id'],
             );
             $addActivityData = $model->table('conf_activity_father')->add($DataActivityFather);
             if(!$addActivityData){
                 $Obj->showmessage('添加失败');
             }else{
                 $Obj->showmessage('添加成功','back');
             }

        }
        $this->assign('catChannel',$catChannel);
        $this->display('add');
    }


    //修改
    public function edit(){
        $obj = new \Common\Lib\func();
        $Id = I('param.Id',0,'intval');
        $UsersInfo = $this->userInfo;
        $catChannelField = array(
            'name',
            'account',
            'id',
        );
        $catChannel = M('jy_admin_users')
            ->where('channel = 2')
            ->field($catChannelField)
            ->select();
        if ($Id<=0){
            $obj->showmessage('非法操作');
        }
        //查询信息
        $ActivityFatherListField = array(
            'Id',
            'AbroadTitle',
            'ShowStartTime',
            'ShowEndTime',
            'WithinTitle',
            'Style',
            'Hot',
            'Channel',
            'Sort',
        );
        $ActivityFatherList = M('conf_activity_father')
                              ->where('Id = '.$Id)
                              ->field($ActivityFatherListField)
                              ->find();
        if(IS_POST){
            $AbroadTitle   = I('param.AbroadTitle','','trim');
            $ShowStartTime = I('param.ShowStartTime','','trim');
            $ShowEndTime   = I('param.ShowEndTime','','trim');
            $WithinTitle  = I('param.WithinTitle','','trim');
            $Hot           = I('param.Hot',1,'intval');
            $Sort          = I('param.Sort',0,'intval');
            $ConfStatus    = I('param.ConfStatus',1,'intval');
            $Channel       = I('param.Channel','','trim');

            $DataUp = array(
                'AbroadTitle'   =>  $AbroadTitle,
                'ShowStartTime' =>  $ShowStartTime,
                'ShowEndTime'   =>  $ShowEndTime,
                'Hot'           =>  $Hot,
                'Sort'          =>  $Sort,
                'ConfStatus'    =>  $ConfStatus,
                'Channel'       =>  $Channel,
                'WithinTitle'   =>  $WithinTitle,
                'Aname'         =>  $UsersInfo['name'],
                'Aid'           =>  $UsersInfo['id'],
            );
            $UpData = M('conf_activity_father')->where('Id = '.$Id)->save($DataUp);
            if($UpData !== false) {
                $obj->showmessage('修改成功', 'back');
            }else{
                $obj->showmessage('修改失败');
            }
        }
        $this->assign('info',$ActivityFatherList);
        $this->assign('catChannel',$catChannel);
        $this->display('edit');
    }


    //删除
    public function del(){
        $Id = I('param.Id',0,'intval');

        if($Id == 0){
            echo  0;
        }else{
            $Model = new Model();
            $Model->startTrans();
            $DelFather = $Model->table('conf_activity_father')->where('Id = '.$Id)->delete();

            $catSon = $Model->table('conf_activity_son')->where('FatherID = '.$Id)->select();
            $DelSon = true;
            if(!empty($catSon)){
                $DelSon    = $Model->table('conf_activity_son')->where('FatherID = '.$Id)->delete();
            }
            if($DelFather && $DelSon){
                $Model->commit();
                echo 1;
            }else{
                $Model->rollback();
                echo 0;
            }
        }
        exit();
    }

    //验证类型
    public function Verification(){
        $Type        =  I('param.Type',0,'intval');
        $Channel     =  I('param.Channel','','trim');
        $CpChannel   = I('param.CpChannel','','trim');
        $Cp          = I('param.IsCp',0,'intval');

        $result = 1;
        if($Type == ''){
            $result = 0;
            goto end;
        }
        $activityFatherList = M('conf_activity_father')
            ->where('Style = '.$Type.' and Channel =  "'.$Channel.'"')
            ->field('id')
            ->find();
        if($Cp == 2){
            $CatCpChannel = M('conf_activity_father')
                ->where('Style = '.$Type.' and Channel = "'.$CpChannel.'"')
                ->field('id')
                ->find();
            if(empty($CatCpChannel)){
                $result =  3;
                goto  end;
            }
        }

        if(!empty($activityFatherList)){
            $result =  2;
            goto  end;
        }
        end:
          echo $result;
          exit();
    }
    //添加列表
    public function addList(){
        $Obj = new \Common\Lib\func();
        $UsersInfo = $this->userInfo;
        $Id     = I('param.Id',0,'intval');
        $Style  = I('param.Style',0,'intval');
        if($Id<=0 || $Style <=0){
            $Obj->showmessage('非法操作');
        }
        $catGoodsAll =
            M('jy_goods_all')
            ->where('IsDel = 1')
            ->field('Id,Type,GetNum,Code,Name')
            ->select();
        //查询类型值
        $catStlyeValue = M('conf_conf_activity_value')
                         ->where('Style = '.$Style)
                        ->field(array(
                            'Name',
                            'Value'
                        ))->select();
        if(IS_POST) {
            $GiveInfo = I('param.GiveInfo', '', 'trim');
            if (!empty($GiveInfo)) {
                $GoodsID = array();
                foreach ($GiveInfo as $k => $v) $GoodsID[$v['GoodsID']] = $v['GoodsID'];
                $GoodsID = implode(',', $GoodsID);
                if (!empty($GoodsID)) {
                    //查询物品
                    $catGoodsInfo = M('jy_goods_all')
                        ->field(array(
                            'Code',
                            'Id',
                            'GetNum',
                            'Name',
                        ))
                        ->where('Id in (' . $GoodsID . ')  and  IsDel = 1')
                        ->select();
                    foreach ($GiveInfo as $k => $v) {
                        foreach ($catGoodsInfo as $key => $val) {
                            if ($val['Id'] == $v['GoodsID']) {
                                $GiveInfo[$k]['Code'] = $val['Code'];
                                $GiveInfo[$k]['GetNum'] = $val['GetNum'];
                                $GiveInfo[$k]['Name'] = $val['Name'];
                            }
                        }
                    }
                }
            }
            $data = array(
                'FatherID' => I('param.Id', 0, 'intval'),
                'SonTitle' => I('param.SonTitle', '', 'trim'),
                'Schedule' => I('param.Schedule', 0, 'intval'),
                'ImgCode' => I('param.ImgCode', '', 'trim'),
                'GiveInfo' => json_encode($GiveInfo),
                'Sort' => I('param.Sort', 0, 'intval'),
                'TypeCode' => I('param.TypeCode', 0, 'intval'),
                'Explain' => I('param.Explain', '', 'trim'),
                'Jump' => I('param.Jump', 0, 'intval'),
                'ConfStatus' => I('param.ConfStatus', 0, 'intval'),
                'Aname' => $UsersInfo['name'],
                'Aid' => $UsersInfo['id'],
            );
            $addData = M('conf_activity_son')->add($data);
            if ($addData) {
                $Obj->showmessage('添加成功', 'back');
            } else {
                $Obj->showmessage('添加失败');
            }
        }
        $this->assign('Id',$Id);
        $this->assign('GoodsAllList',$catGoodsAll);
        $this->assign('StlyeValue',$catStlyeValue);
        $this->assign('Style',$Style);
        $this->display();
    }
    //添加图片
    public function addImg(){
        $Obj = new \Common\Lib\func();
        $UsersInfo = $this->userInfo;
        $Id     = I('param.Id',0,'intval');
        $Style  = I('param.Style',0,'intval');
        if($Id<=0 || $Style <=0){
            $Obj->showmessage('非法操作');
        }
        $catGoodsAll =
            M('jy_goods_all')
                ->where('IsDel = 1')
                ->field('Id,Type,Code,GetNum,Name')
                ->select();
        //查询类型值
        $catStlyeValue = M('conf_conf_activity_value')
            ->where('Style = '.$Style)
            ->field(array(
                'Name',
                'Value'
            ))->select();
        if(IS_POST){
            $GiveInfo       = I('param.GiveInfo','','trim');
            $data = array(
                'FatherID'      =>  I('param.Id',0,'intval'),
                'SonTitle'      =>  I('param.SonTitle','','trim'),
                'Schedule'      =>  I('param.Schedule',0,'intval'),
                'ImgCode'       =>  I('param.ImgCode','','trim'),
                'GiveInfo'      =>  json_encode($GiveInfo) ,
                'Sort'          =>  I('param.Sort',0,'intval'),
                'TypeCode'      =>  I('param.TypeCode',0,'intval'),
                'Explain'       =>  I('param.Explain','','trim'),
                'Jump'          =>  I('param.Jump',1,'intval'),
                'ConfStatus'    =>  I('param.ConfStatus',0,'intval'),
                'Aname'         =>  $UsersInfo['name'],
                'Aid'           =>  $UsersInfo['id'],
            );
            $addData = M('conf_activity_son')->add($data);
            if($addData){
                $Obj->showmessage('添加成功','back');
            }else{
                $Obj->showmessage('添加失败');
            }
        }
        $this->assign('Id',$Id);
        $this->assign('GoodsAllList',$catGoodsAll);
        $this->assign('StlyeValue',$catStlyeValue);
        $this->assign('Style',$Style);
        $this->display();
    }
    //添加抽奖
    public function addLuckDraw(){
        $Obj = new \Common\Lib\func();
        $UsersInfo = $this->userInfo;
        $Id     = I('param.Id',0,'intval');
        $Style  = I('param.Style',0,'intval');
        if($Id<=0 || $Style <=0){
            $Obj->showmessage('非法操作');
        }
        $catGoodsAll =
            M('jy_goods_all')
                ->where('IsDel = 1')
                ->field('Id,Type,Code,GetNum,Name')
                ->select();
        //查询类型值
        $catStlyeValue = M('conf_conf_activity_value')
            ->where('Style = '.$Style)
            ->field(array(
                'Name',
                'Value'
            ))->select();
        if(IS_POST){
            $GiveInfo       = I('param.GiveInfo','','trim');
            if (!empty($GiveInfo)) {
                $GoodsID = array();
                foreach ($GiveInfo as $k => $v) $GoodsID[$v['GoodsID']] = $v['GoodsID'];
                $GoodsID = implode(',', $GoodsID);
                if (!empty($GoodsID)) {
                    //查询物品
                    $catGoodsInfo = M('jy_goods_all')
                        ->field(array(
                            'Code',
                            'Id',
                            'GetNum',
                            'Name',
                        ))
                        ->where('Id in (' . $GoodsID . ')  and  IsDel = 1')
                        ->select();
                    foreach ($GiveInfo as $k => $v) {
                        foreach ($catGoodsInfo as $key => $val) {
                            if ($val['Id'] == $v['GoodsID']) {
                                $GiveInfo[$k]['Code'] = $val['Code'];
                                $GiveInfo[$k]['GetNum'] = $val['GetNum'];
                                $GiveInfo[$k]['Name'] = $val['Name'];
                            }
                        }
                    }
                }
            }
            $data = array(
                'FatherID'      =>  I('param.Id',0,'intval'),
                'SonTitle'      =>  I('param.SonTitle','','trim'),
                'Schedule'      =>  I('param.Schedule',0,'intval'),
                'ImgCode'       =>  I('param.ImgCode','','trim'),
                'GiveInfo'      =>  json_encode($GiveInfo) ,
                'Sort'          =>  I('param.Sort',0,'intval'),
                'TypeCode'      =>  I('param.TypeCode',0,'intval'),
                'Explain'       =>  I('param.Explain','','trim'),
                'Jump'          =>  I('param.Jump',1,'intval'),
                'ConfStatus'    =>  I('param.ConfStatus',0,'intval'),
                'Aname'         =>  $UsersInfo['name'],
                'Aid'           =>  $UsersInfo['id'],
            );
            $addData = M('conf_activity_son')->add($data);
            if($addData){
                $Obj->showmessage('添加成功','back');
            }else{
                $Obj->showmessage('添加失败');
            }
        }
        $this->assign('Id',$Id);
        $this->assign('GoodsAllList',$catGoodsAll);
        $this->assign('StlyeValue',$catStlyeValue);
        $this->assign('Style',$Style);
        $this->display();
    }


    //修改列表
    public function editList(){
        $Id = I('param.Id',0,'intval');

        $Style  = I('param.Style',0,'intval');

        $Obj = new \Common\Lib\func();
        $UsersInfo = $this->userInfo;
        if($Id <= 0 || $Style <=0){
            $Obj->showmessage('非法操作！');
        }
        //查询类型值
        $catStlyeValue = M('conf_conf_activity_value')
            ->where('Style = '.$Style)
            ->field(array(
                'Name',
                'Value'
            ))->select();

        //查询物品
        $catGoodsAll =
            M('jy_goods_all')
                ->where('IsDel = 1')
                ->field('Id,Type,Code,GetNum,Name')
                ->select();
        //查询信息
        $info = M('conf_activity_son')
                ->where('Id = '.$Id)
                ->field(array(
                    'Id',
                    'SonTitle',
                    'Schedule',
                    'ImgCode',
                    'GiveInfo',
                    'Jump',
                    'Sort',
                    'TypeCode',
                    'Explain',
                    'ConfStatus',
                    'Aname',
                    'Aid',
                ))
                ->find();




        if(IS_POST){
            $GiveInfo       = I('param.GiveInfo','','trim');



            if (!empty($GiveInfo)) {
                $GoodsID = array();
                foreach ($GiveInfo as $k => $v) $GoodsID[$v['GoodsID']] = $v['GoodsID'];
                $GoodsID = implode(',', $GoodsID);
                if (!empty($GoodsID)) {
                    //查询物品
                    $catGoodsInfo = M('jy_goods_all')
                        ->field(array(
                            'Code',
                            'Id',
                            'GetNum',
                            'Name',
                        ))
                        ->where('Id in (' . $GoodsID . ')  and  IsDel = 1')
                        ->select();
                    foreach ($GiveInfo as $k => $v) {
                        foreach ($catGoodsInfo as $key => $val) {
                            if ($val['Id'] == $v['GoodsID']) {
                                $GiveInfo[$k]['Code'] = $val['Code'];
                                $GiveInfo[$k]['GetNum'] = $val['GetNum'];
                                $GiveInfo[$k]['Name'] = $val['Name'];
                            }
                        }
                    }
                }
            }
            $data = array(
                'SonTitle'      =>  I('param.SonTitle','','trim'),
                'Schedule'      =>  I('param.Schedule',0,'intval'),
                'ImgCode'       =>  I('param.ImgCode','','trim'),
                'GiveInfo'      =>  json_encode($GiveInfo) ,
                'Sort'          =>  I('param.Sort',0,'intval'),
                'Explain'       =>  I('param.Explain','','trim'),
                'Jump'          =>  I('param.Jump',1,'intval'),
                'ConfStatus'    =>  I('param.ConfStatus',0,'intval'),
                'Aname'         =>  $UsersInfo['name'],
                'Aid'           =>  $UsersInfo['id'],
            );
            $upData = M('conf_activity_son')
                      ->where('Id = '.$Id)
                      ->save($data);
            if($upData !== false  ){
                $Obj->showmessage('修改成功','back');
            }else{
                $Obj->showmessage('修改失败');
            }
        }
        $this->assign('GoodsAllList',$catGoodsAll);
        $this->assign('info',$info);
        $this->assign('StlyeValue',$catStlyeValue);
        $this->assign('GiveInfo',json_decode($info['GiveInfo'],true));
        $this->display();
    }

    //修改转盘
    public function editLuckDraw(){
        $Id = I('param.Id',0,'intval');
        $Style  = I('param.Style',0,'intval');
        $Obj = new \Common\Lib\func();
        $UsersInfo = $this->userInfo;
        if($Id <= 0 || $Style <=0){
            $Obj->showmessage('非法操作！');
        }
        //查询类型值
        $catStlyeValue = M('conf_conf_activity_value')
            ->where('Style = '.$Style)
            ->field(array(
                'Name',
                'Value'
            ))->select();

        //查询物品
        $catGoodsAll =
            M('jy_goods_all')
                ->where('IsDel = 1')
                ->field('Id,Type,Code,GetNum,Name')
                ->select();
        //查询信息
        $info = M('conf_activity_son')
            ->where('Id = '.$Id)
            ->field(array(
                'Id',
                'SonTitle',
                'Schedule',
                'ImgCode',
                'GiveInfo',
                'Sort',
                'Jump',
                'TypeCode',
                'Explain',
                'ConfStatus',
                'Aname',
                'Aid',
            ))
            ->find();
        if(IS_POST){
            $GiveInfo       = I('param.GiveInfo','','trim');
            if (!empty($GiveInfo)) {
                $GoodsID = array();
                foreach ($GiveInfo as $k => $v) $GoodsID[$v['GoodsID']] = $v['GoodsID'];
                $GoodsID = implode(',', $GoodsID);
                if (!empty($GoodsID)) {
                    //查询物品
                    $catGoodsInfo = M('jy_goods_all')
                        ->field(array(
                            'Code',
                            'Id',
                            'GetNum',
                            'Name',
                        ))
                        ->where('Id in (' . $GoodsID . ')  and  IsDel = 1')
                        ->select();
                    foreach ($GiveInfo as $k => $v) {
                        foreach ($catGoodsInfo as $key => $val) {
                            if ($val['Id'] == $v['GoodsID']) {
                                $GiveInfo[$k]['Code'] = $val['Code'];
                                $GiveInfo[$k]['GetNum'] = $val['GetNum'];
                                $GiveInfo[$k]['Name'] = $val['Name'];
                            }
                        }
                    }
                }
            }
            $data = array(
                'SonTitle'      =>  I('param.SonTitle','','trim'),
                'Schedule'      =>  I('param.Schedule',0,'intval'),
                'ImgCode'       =>  I('param.ImgCode','','trim'),
                'GiveInfo'      =>  json_encode($GiveInfo) ,
                'Sort'          =>  I('param.Sort',0,'intval'),
                'Explain'       =>  I('param.Explain','','trim'),
                'Jump'          =>  I('param.Jump',1,'intval'),
                'ConfStatus'    =>  I('param.ConfStatus',1,'intval'),
                'Aname'         =>  $UsersInfo['name'],
                'Aid'           =>  $UsersInfo['id'],
            );
            $upData = M('conf_activity_son')
                ->where('Id = '.$Id)
                ->save($data);
            if($upData !== false  ){
                $Obj->showmessage('修改成功','back');
            }else{
                $Obj->showmessage('修改失败');
            }
        }
        $this->assign('GoodsAllList',$catGoodsAll);
        $this->assign('StlyeValue',$catStlyeValue);
        $this->assign('GiveInfo',json_decode($info['GiveInfo'],true));
        $this->assign('info',$info);
        $this->display();

    }
    //添加图片
    public function editImg(){

        $Id = I('param.Id',0,'intval');
        $Style  = I('param.Style',0,'intval');
        $Obj = new \Common\Lib\func();
        $UsersInfo = $this->userInfo;
        if($Id <= 0 || $Style <=0){
            $Obj->showmessage('非法操作！');
        }
        //查询类型值
        $catStlyeValue = M('conf_conf_activity_value')
            ->where('Style = '.$Style)
            ->field(array(
                'Name',
                'Value'
            ))->select();

        //查询物品
        $catGoodsAll =
            M('jy_goods_all')
                ->where('IsDel = 1')
                ->field('Id,Type,GetNum,Name')
                ->select();
        //查询信息
        $info = M('conf_activity_son')
            ->where('Id = '.$Id)
            ->field(array(
                'Id',
                'SonTitle',
                'Schedule',
                'ImgCode',
                'GiveInfo',
                'Sort',
                'Jump',
                'TypeCode',
                'Explain',
                'ConfStatus',
                'Aname',
                'Aid',
            ))
            ->find();
        if(IS_POST){
            $GiveInfo       = I('param.GiveInfo','','trim');
            $data = array(
                'SonTitle'      =>  I('param.SonTitle','','trim'),
                'Schedule'      =>  I('param.Schedule',0,'intval'),
                'ImgCode'       =>  I('param.ImgCode','','trim'),
                'Sort'          =>  I('param.Sort',0,'intval'),
                'Explain'       =>  I('param.Explain','','trim'),
                'Jump'          =>  I('param.Jump',1,'intval'),
                'ConfStatus'    =>  I('param.ConfStatus',0,'intval'),
                'Aname'         =>  $UsersInfo['name'],
                'Aid'           =>  $UsersInfo['id'],
            );
            $upData = M('conf_activity_son')
                ->where('Id = '.$Id)
                ->save($data);
            if($upData !== false  ){
                $Obj->showmessage('修改成功','back');
            }else{
                $Obj->showmessage('修改失败');
            }
        }
        $this->assign('GoodsAllList',$catGoodsAll);
        $this->assign('StlyeValue',$catStlyeValue);
        $this->assign('info',$info);
        $this->display();

    }

    public function delSon(){
        $Id = I('param.Id',0,'intval');
        $result = 1;
        if($Id <= 0){
            $result = 0;
            goto end;
        }
        $delData = M('conf_activity_son')
                   ->where('Id = '.$Id)
                   ->delete();
        if(!$delData){
            $result = 0;
            goto end;
        }
        end:
        echo $result;
        exit();
    }

    public function TypeCodeVerification(){
        $TypeCode   = I('param.TypeCode','','trim');
        $result = 1;
        if(  $TypeCode ==""  ){
            $result  = 0;
            goto  end;
        }
        $catData = M('conf_activity_son')->where('TypeCode = '.$TypeCode)->field('Id')->find();
        if(!empty($catData)){
            $result = 0;
            goto  end;
        }
        end:
        echo $result;
        exit();
    }

    //发布全部
    public function sendAll(){
       $Id = I('param.Id',0,'intval');
       $result = 1;
       if($Id <=0 ){
          $result  = 0;
          goto  end;
       }

       $model = new Model();
       $model->startTrans();
       $UpFather = $model
                   ->table('conf_activity_father')
                   ->where('Id = '.$Id)
                   ->save(array(
                       'ConfStatus'=>2,
                   ));
        $UpSon =  $model
                  ->table('conf_activity_son')
                  ->where('FatherID = '.$Id)
                  ->save(array(
                    'ConfStatus'=>2,
                   ));
        if($UpFather !==false && $UpSon !== false ){
            $result  = 1;
            $model->commit();
            goto  end;
        }else{
            $model->rollback();
        }

       end:
       echo $result;
       exit();
    }

    //发所有
    public function send(){
        $Id = I('param.Id',0,'intval');
        $result = 1;
        if($Id <=0 ){
            $result  = 0;
            goto  end;
        }
        $model = new Model();
        $UpSon =  $model
            ->table('conf_activity_son')
            ->where('Id = '.$Id)
            ->save(array(
                'ConfStatus'=>2,
            ));
        if($UpSon === false ){
            $result  = 0;
            goto  end;
        }
        end:
        echo $result;
        exit();
    }
    public function catSon(){
        $Id = I('param.Id',0,'intval');
        $Obj = new \Common\Lib\func();
        if($Id<=0){
            $Obj->showmessage('非法操作！');
        }

        $catData = M('conf_activity_son')
                   ->where('FatherID = '.$Id)
                   ->field(
                       array(
                           'SonTitle',
                           'ImgCode',
                           'Schedule',
                           'GiveInfo',
                           'ConfStatus',
                           'Jump',
                           'Explain',
                           'TypeCode',
                       )
                   )
                   ->select();
        foreach ($catData as $k=>$v){
            $GoodsName = array();
            foreach (json_decode($v['GiveInfo'],true) as $key => $val){
                $GoodsName[] =   $val['GoodsName'];
            }
            $catData[$k]['GoodsNameList'] =   implode('、',$GoodsName);
        }
        $this->assign('info',$catData);
        $this->display();

    }

}