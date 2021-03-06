<?php
/***
 *  金立支付回调
 **/
namespace Jy_Thirdpay\Controller;
use Protos\OptReason;
use Protos\OptSrc;
use Protos\PBS_UsrDataOprater;
use Protos\PBS_UsrDataOpraterReturn;
use Protos\UsrDataOpt;
use RedisProto\RPB_PlayerData;
use Think\Controller;
use Think\Model;
class JinPayController extends Controller {
    public function index(){
        $ObjFun = new \Common\Lib\func();
        $dataThirdpay = $_POST;
        if(C('ACCESS_lOGS')){
            $dir = C('YQ_ROOT').'Log/pay/'.date('Y').'/'.date('m').'/'.date('d').'/';
            $ObjFun->record_log($dir,'access_'.date('Ymd').'.log',json_encode($dataThirdpay));
        }
       // $dataThirdpay =  file_get_contents('php://input');
        if(!is_array($dataThirdpay)){
            $dataThirdpay = json_decode($dataThirdpay,true);
        }
        $msgArr = array(
            2001=>'支付成功！',
            3000=>'支付并发',
            3001=>'服务器无响应！',
            3002=>'服务器无响应！',
            3003=>'服务器错误！',
            3004=>'服务器无响应',
            3005=>'服务器无响应',
            3006=>'服务器错误！',
            3007=>'支付并发',
            4001=>'回调数据为空！',
            4002=>'订单不存在！',
            5001=>'订单不存在',
            5002=>'支付信息不存在！',
            5003=>'商品部存在！',
            5004=>'vip配置不存在！',
            7001=>'价格不匹配！',
            7002=>'验签失败！'
        );
        $result = 2001;
        if($dataThirdpay == null){
            $result = 4001;
            goto  failed;
        }
        //订单号
        $OrderID        = $dataThirdpay['out_order_no'];

        //金额
        $money          = $dataThirdpay['deal_price'];

        //实例化数据
        $model = new Model();
        //查询订单信息
        $CatUsersOrderInfoField = array(
            'VipLevel',
            'VipExp',
            'playerid',
            'appuserid',
            'Price',
            'Status',
            'PayID',
            'Form',
            'Version',
        );
        $CatUsersOrderInfo = $model
            ->table('jy_users_order_info')
            ->where(' PlatformOrder = "'.$OrderID.'"')
            ->field($CatUsersOrderInfoField)
            ->find();
        if(empty($CatUsersOrderInfo)){
            $result = 5001;
            goto failed;
        }

        if($CatUsersOrderInfo['Status'] == 2){
            goto success;
        }
        $appuserid      = $CatUsersOrderInfo['appuserid'];
        $appuserid      = explode('#',$appuserid);
        $playerid       = $appuserid[0];
        //商品ID
        $GoosID         = $appuserid[1];
        //金额是否先匹配
        if($CatUsersOrderInfo['Price']  !=  $money){
            $result = 7001;
            goto OrderSave;
        }
        //查询支付信息
        $ThirdpayField = array(
            'public',
            'private',
            'appid',
        );
        $Thirdpay = $model
            ->table('jy_thirdpay')
            ->field($ThirdpayField)
            ->where('Id = '.$CatUsersOrderInfo['PayID'].' and  IsDel = 1')
            ->find();
        if(empty($Thirdpay)){
            $result = 5002;
            goto OrderSave;
        }
        //验签
        $JinPayRsaverify = $ObjFun->JinPayRsaverify($Thirdpay['public'],$dataThirdpay);
        if(!$JinPayRsaverify){
            $result = 7002;
            goto OrderSave;
        }

        //支付状态  0 成功  1  失败
        $TransdataResult = 0;
        //查询商品
        $GoodsInfoField = array(
            'GoodsName',
            'GoodsCode',
            'GetNum',
            'Proportion',
            'GoodsID',
            'IsGive',
            'Number',
            'Type',
        );
        $GoodsInfo = $model
            ->table('jy_users_order_goods')
            ->where('playerid = '.$CatUsersOrderInfo['playerid'].' and  PlatformOrder = "'.$OrderID.'"')
            ->field($GoodsInfoField)
            ->select();
        if(empty($GoodsInfo)){
            $result = 5003;
            goto OrderSave;
        }

        /**
         * 服务器查询
         * statr
         */
        $dataJinpayLog = array(
            'api_key'=>$dataThirdpay['api_key'],
            'close_time'=>$dataThirdpay['close_time'],
            'create_time'=>$dataThirdpay['create_time'],
            'deal_price'=>$dataThirdpay['deal_price'],
            'out_order_no'=>$dataThirdpay['out_order_no'],
            'pay_channel'=>$dataThirdpay['pay_channel'],
            'submit_time'=>$dataThirdpay['submit_time'],
            'user_id'=>$dataThirdpay['user_id'],
            'sign'=>$dataThirdpay['sign'],
            'DateTime'=>$dataThirdpay['DateTime'],
        );
        $addJinpayLog = M('jy_jinpay_log')->add($dataJinpayLog);

        $ObjFun->ProtobufObj(array(
            'Protos/PBS_UsrDataOprater.php',
            'Protos/PBS_UsrDataOpraterReturn.php',
            'RedisProto/RPB_PlayerData.php',
            'Protos/OptSrc.php',
            'Protos/UsrDataOpt.php',
            'OptReason.php',
            'PB_PlayerVip.php',
            'PB_HallNotify.php',
            'RPB_PlayerNumerical.php',
            'PB_Email.php',
            'PB_Item.php',
            'PB_ResourceChange.php',
            'PB_ErrorCode.php',
            'PB_BuyGoods.php',
            'EmailType.php',
        ));
        //实例化对象
        $UsrDataOprater         =   new PBS_UsrDataOprater();
        $UsrDataOpraterReturn   =   new PBS_UsrDataOpraterReturn();
        $PlayerData             =   new RPB_PlayerData();
        $EmailType              =   new \EmailType();
        $Email                  =   new \PB_Email();
        $OptSrc                 =   new OptSrc();
        $UsrDataOpt             =   new UsrDataOpt();
        $ErrorCode              =   new \PB_ErrorCode();
        $BuyGoods               =   new \PB_BuyGoods();
        $PB_ResourceChange      =   new \PB_ResourceChange();
        $OptReason              =   new \OptReason();
        $PB_HallNotify  = new \PB_HallNotify();
        $PB_PlayerVip   = new \PB_PlayerVip();
        //设置protocbuf
        $UsrDataOprater->setPlayerid($playerid);
        $UsrDataOprater->setSrc($OptSrc::Src_PHP);
        $UsrDataOprater->setOpt($UsrDataOpt::Modify_Player);
        if($TransdataResult == 0){
            //支付成功
            $BuyGoods->setErr($ErrorCode::Error_success);
            $BuyGoods->setGoodsid($GoosID);
            //判断是否升级
            $VipLevel =    $CatUsersOrderInfo['VipLevel'];
            $VipExp   =    $CatUsersOrderInfo['VipExp'];
            $UpVipExp =    $VipExp+ $CatUsersOrderInfo['Price'];
            //查询Vip信息
            $VipInfoField = array(
                'level',
                'experience',
            );
            $VipInfo = $model
                ->table('jy_vip_info')
                ->field($VipInfoField)
                ->where('experience <= '.$UpVipExp)
                ->order('level desc')
                ->find();
            if(empty($VipInfo)){
                $result = 5004;
                goto OrderSave;
            }
            $PlayerData->setVipExp($UpVipExp);
            $Statuslevel = 0;
            if($VipInfo['level'] != $VipLevel){
                $Statuslevel = $VipInfo['level'];
            }else{
                $PB_PlayerVip->setVip($VipLevel);
                $Statuslevel = $VipLevel;
            }
            $PB_PlayerVip->setVip($Statuslevel);
            $PB_PlayerVip->setExp($UpVipExp);
            //判断是否已经领取 1-否  2-是
            $Status = false;
            if($Statuslevel>0){
                $catVipRewardlogField = array(
                    'Id',
                );
                $strtotime = strtotime(date('Y-m-d'),time());
                $StartTime = date('Y-m-d H:i:s',$strtotime);
                $EndTime   = date('Y-m-d H:i:s',$strtotime+24*60*60);
                $catVipRewardlog = M('jy_vip_reward_log')
                    ->where('playerid = '.$playerid.'  and  DateTime  >= str_to_date("'.$StartTime.'","%Y-%m-%d %H:%i:%s")  and   DateTime <  str_to_date("'.$EndTime.'","%Y-%m-%d %H:%i:%s")')
                    ->field($catVipRewardlogField)
                    ->find();
                if(empty($catVipRewardlog)){
                    $Status=true;
                }
            }
            $PB_PlayerVip->setIsCanReward($Status);
            $PB_HallNotify->setPlayerVip($PB_PlayerVip);
            //是否升级 1 否  2 是
            if($VipInfo['level'] != $VipLevel){
                $PlayerData->setVip($VipInfo['level']);
                //发送邮件
                $Email->setSender('系统');
                $Email->setType($EmailType::EmailType_Sys);
                $Email->setTitle('vip等级提升');
                $Email->setData('恭喜您，vip等级提升到'.$VipInfo['level'].'，相关权限请查看vip等级信息列表。');
                $UsrDataOprater->setSendEmail($Email);
            }
            //是否月卡
            if($CatUsersOrderInfo['Form'] == 2){
                $UsrDataOprater->setReason($OptReason::buy_yueka_ok);
                $McOvertime = strtotime(date('Y-m-d',time()))+30*24*60*60;
                $PlayerData->setMcOvertime($McOvertime);
                $PlayerData->setIsMc(true);
                $dataLogUsersShop['Number'] = 1;
                $dataLogUsersShop['Type']   = $GoodsInfo[0]['Type'];
                $dataLogUsersShop['Code']   = $GoodsInfo[0]['GoodsCode'];
            }
            //添加物品
            $IsGold = 1; //是否添加过金币 1-否 2是 注释：商城
            if($CatUsersOrderInfo['Form'] == 3 || $CatUsersOrderInfo['Form'] == 1){
                if($CatUsersOrderInfo['Form'] == 1){
                    $UsrDataOprater->setReason($OptReason::first_pay);
                }
                if($CatUsersOrderInfo['Form'] == 3){
                    $UsrDataOprater->setReason($OptReason::mall_reward_sdk);
                }
                foreach ($GoodsInfo as $k=>$v){
                    if($CatUsersOrderInfo['Form'] == 3){
                        $num = $v['GetNum']*$v['Number']+($v['GetNum']*$v['Proportion'])*$v['Number']/100;
                    }else{
                        $num =  $v['GetNum']*$v['Number'];
                    }
                    if($v['IsGive'] == 1){
                        $dataLogUsersShop['Number'] = $num;
                        $dataLogUsersShop['Type']   = $v['Type'];
                        $dataLogUsersShop['Code']   = $v['GoodsCode'];
                    }
                    switch ($v['Type']){
                        //金币
                        case  1:
                            $PlayerData->setGold($num);
                            if($CatUsersOrderInfo['Form'] == 3){
                                $IsGold = 2;
                            }
                            $PB_Item = new \PB_Item();
                            $PB_Item->setNum($num);
                            $PB_Item->setId(8);
                            $PB_ResourceChange->appendItems($PB_Item);
                            break;
                        //砖石
                        case  2:
                            $PlayerData->setDiamond($num);
                            $PB_Item = new \PB_Item();
                            $PB_Item->setNum($num);
                            $PB_Item->setId(9);
                            $PB_ResourceChange->appendItems($PB_Item);
                            break;
                        //道具
                        case  3:
                            $Item = new \PB_Item();
                            $Item->setId($v['GoodsCode']);
                            $Item->setNum($num);
                            $UsrDataOprater->appendItemOpt($Item);
                            $PB_Item = new \PB_Item();
                            $PB_Item->setNum($num);
                            $PB_Item->setId($v['GoodsCode']);
                            $PB_ResourceChange->appendItems($PB_Item);
                            break;
                    }
                }
            }
            $PlayerData->setRmb($money);
            if($IsGold == 2){
                $OptReason  =  new \OptReason();
                $UsrDataOprater->setReason($OptReason::pay_gold);
            }
            if($CatUsersOrderInfo['Form'] == 1){
                $PB_ResourceChange->setReason($OptReason::first_pay);
            }elseif ($CatUsersOrderInfo['Form'] == 2){
                $PlayerData->setDiamond(100);
                $Item = new \PB_Item();
                $Item->setId(9);
                $Item->setNum(100);
                $PB_ResourceChange->appendItems($Item);
                $PB_ResourceChange->setReason($OptReason::buy_yueka_ok);
            }elseif ($CatUsersOrderInfo['Form'] == 3){
                $PB_ResourceChange->setReason($OptReason::mall_reward_sdk);
            }
            $PB_ResourceChange->setPlayerid($playerid);
            $PB_HallNotify->setResChanged($PB_ResourceChange);
            $PB_HallNotify->setBuyNotify($BuyGoods);
            $UsrDataOprater->setNotify($PB_HallNotify);
            $UsrDataOprater->setPlayerData($PlayerData);

        }else{
            //支付失败
            $BuyGoods->setErr($ErrorCode::Error_buy_fail);
            $BuyGoods->setGoodsid($GoosID);
            $UsrDataOprater->setBuyGoodsNotify($BuyGoods);
            $result = 7003;
            goto OrderSave;
        }
        //反序列化

        $serialize = $UsrDataOprater->serializeToString();
        $Header = array(
            'PBName:'.'protos.PBS_UsrDataOprater',
            'PBSize:'.strlen($serialize),
            'UID:'.$playerid,
            'PBUrl:'.CONTROLLER_NAME.ACTION_NAME,
            'Version:'.$CatUsersOrderInfo['Version'],
        );
        //发送请求
        $Respond =  $ObjFun->ProtobufSend($Header,$serialize);
        if(strlen($Respond)!=0 && $Respond != 504){
            $UsrDataOpraterReturn->parseFromString($Respond);
            $ReplyCode = $UsrDataOpraterReturn->getCode();
            if($ReplyCode != 1 ){
                $result = 3006;
                goto OrderSave;
            }
        }else{
            $result = 3004;
            goto OrderSave;
        }

        $MoreThan = $playerid%10;
        if($result == 2001 || $result == 1){
            //开启事物
            $model->startTrans();
            $dataLogUsersShop['playerid'] = $playerid;
            $dataLogUsersShop['GoodsID'] = $GoosID;
            $dataLogUsersShop['Price'] = $money;
            $dataLogUsersShop['Form'] = $CatUsersOrderInfo['Form'];
            $addLogUsersShop  = M('log_users_shop_'.$MoreThan)->add($dataLogUsersShop);

            //修改订单
            $dataUsersOrderInfo['CallbackTime']  = date('Y-m-d H:i:s',time());
            $dataUsersOrderInfo['PayType']       = 0;
            $dataUsersOrderInfo['MessAge']       = '状态码：'.$result.'说明：'.$msgArr[$result].';';
            $dataUsersOrderInfo['Status']        = 2;
            $UpUsersOrderInfo = $model
                ->table('jy_users_order_info')
                ->where('playerid  = '.$playerid.'  and    PlatformOrder = "'.$OrderID.'"')
                ->save($dataUsersOrderInfo);

            if($addLogUsersShop && $UpUsersOrderInfo){
                $model->commit();
                goto  success;
            }else{
                $result = 3000;
                $model->rollback();
                goto OrderSave;

            }
        }else{
            goto OrderSave;
        }

        //$ObjFun->JinPayRsaverify()
        success:
        $response = array(
            'result' => $result,
            'msg' => $msgArr[$result],
            'data' => array(),
        );
        echo json_encode($response);
        exit();
        failed:
        $dataApiVisitLog = array(
            'Name'=>'支付订单',
            'Url'=>'/Jy_ThirdpayIapppay/MallShopBack/index',
            'Msg'=>$msgArr[$result],
            'Code'=>$result,
            'TimeOut'=>'',
            'AccessIP'=>$_SERVER['REMOTE_ADDR'],
        );
        $addApiVisitLog = M('jy_api_visit_log')
            ->add($dataApiVisitLog);
        echo 'failed'."\n";
        exit();
        OrderSave:
        $dataUsersOrderInfo = array();   //订单数据
        $dataUsersOrderInfo['CallbackTime']  = date('Y-m-d H:i:s',time());
        $dataUsersOrderInfo['PayType']       = 0;
        $dataUsersOrderInfo['MessAge']       = '状态码：'.$result.'说明：'.$msgArr[$result].';';
        $dataUsersOrderInfo['Status']        = 4;

        $UpUsersOrderInfo = $model
            ->table('jy_users_order_info')
            ->where('playerid  = '.$playerid.'  and    PlatformOrder = "'.$OrderID.'"')
            ->save($dataUsersOrderInfo);
        echo 'failed'."\n";
        exit();

    }
}