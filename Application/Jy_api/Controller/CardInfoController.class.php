<?php
/***
 * 月卡
 * @param array   $msgArr  2*  成功  3.* 超时无响应  4.*丢失或参数缺失  5.* 服务器配置问题  6.*来不明  7.* 权限问题 8.* 配置问题
 * @param int     $page         页码
 * @param int     $num          页数
 * @param int     $channelid    渠道id
 * @param int     $platform     平台号  1-iso  2-安卓
 * @param unknow  $version      版本号
 ***/
namespace Jy_api\Controller;
use Jy_api\Controller\ComController;
use Protos\PBS_UsrDataOprater;
use Protos\PBS_UsrDataOpraterReturn;
use RedisProto\RPB_PlayerData;
use Think\Controller;
use Think\Model;
class CardInfoController extends ComController {
    public function index(){
        $DataInfo       =       $this->DataInfo;
        $msgArr         =       $this->msgArr;
        $obj   = new \Common\Lib\func();
        $result = 2001;
        $info   =  array();
        $msgArr[4006] = '用户信息缺失！';
        $playerid = $DataInfo['playerid'];
        if(empty($playerid)){
            $result = 4006;
            goto response;
        }
        //查询奖励
        $GoodsInfoFile = array(
            'GiveInfo',
            'CurrencyNum',

        );
        $GoodsAll = M('jy_goods_all')
                     ->field($GoodsInfoFile)
                     ->where('ShowType = 3 and  CateGory = 4  and IsDel = 1')
                     ->find();
        $GiveInfo           = json_decode($GoodsAll['GiveInfo'],true);

        $CardGoodsInfo      = array();
        if(!empty($GiveInfo)){
            $GoodID = array();
            foreach ($GiveInfo as $k=>$v){
                $GoodID[] = $v['Id'];
            }
            $CardGoodsInfoFile  = array(
                 'Id',
                 'Name',
                 'GetNum',
                 'ImgCode',
                 'Type',
            );
            $GoodID = implode(',',$GoodID);
            $CardGoodsInfo      =  M('jy_goods_all')
                                    ->field($CardGoodsInfoFile)
                                    ->where('Id in('.$GoodID.')')
                                    ->select();
            if(!empty($CardGoodsInfo)){
                foreach ($CardGoodsInfo as $k=>$v){
                    foreach ($GiveInfo as $key=>$val){
                            if($val['Id'] == $v['Id']){
                                $CardGoodsInfo[$k]['GetNum'] =  $v['GetNum']*$val['GetNum'];
                            }
                    }
                }
            }
        }

        $ShopCard  = 1;     //是否购买过月卡      1 否  2 是
        $IsReceive = 1;     //今天是否领取过月卡  1 否   2 是
        $DayNum    = 0;     //月卡还有剩多少天
        $UsersCardShopLog = M('jy_users_package_shop_log')
            ->field('date_format(DateTime,"%Y-%m-%d") as DateTime')
            ->where('playerid = '.$playerid.' and Type = 2')
            ->order('Id desc')
            ->find();
        if(empty($UsersCardShopLog)){
            $ShopCard  = 1;
            $IsReceive = 1;
            $DayNum    = 0;
        }else{
            //判断月卡是否过期

            $DateTime           =           strtotime($UsersCardShopLog['DateTime']);
            $CurrentTime        =           strtotime(date('Y-m-d',time()));
            $OneDay             =           24*60*60;
            $DayNum             =           ($CurrentTime-$DateTime)/$OneDay;

            if($DayNum > 29){
                $ShopCard  = 1;
                $DayNum    = 0;
                $IsReceive = 1;
            }else{
                $StartTime          =           $CurrentTime+$OneDay;
                $EndTime            =           $CurrentTime-$OneDay;
                $UsersCardReceive = M('jy_users_card_receive_log')
                    ->where('playerid = '.$playerid.' and  DateTime <=  str_to_date("'.$EndTime.'","%Y-%m-%d %H:%i:%s") and  DateTime >= str_to_date("'.$StartTime.'","%Y-%m-%d %H:%i:%s")')
                    ->find();
                if(!empty($UsersCardReceive)){
                    $IsReceive = 2;
                }
                $DayNum  =  30-$DayNum;
            }
        }

        //信息
        $info['GoodsInfo']   = $CardGoodsInfo;
        $info['CurrencyNum'] = $GoodsAll['CurrencyNum'];
        $info['DayNum']      = $DayNum;
        $info['ShopCard']    = $ShopCard;
        $info['IsReceive']   = $IsReceive;

        response:
            $response = array(
                'result' => $result,
                'msg' => $msgArr[$result],
                'sessionid'=>$DataInfo['sessionid'],
                'data' => $info,
            );
            $this->response($response,'json');


    }
}