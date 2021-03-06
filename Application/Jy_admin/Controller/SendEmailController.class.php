<?php
/****
*  发送邮件
**/
namespace Jy_admin\Controller;
use Protos\OptSrc;
use Protos\PBS_SendEmail2All;
use Protos\PBS_SendEmail2AllReturn;
use Protos\PBS_SetServerStateReturn;
use Protos\PBS_UsrDataOprater;
use Protos\PBS_UsrDataOpraterReturn;
use Protos\UsrDataOpt;
use RedisProto\RPB_PlayerData;
use Think\Controller;
class SendEmailController extends ComController {
    public function index(){
        //查询物品
        $Com = D('Com');
        $VersionList = $Com->GetVersionList();
        if(!$VersionList){
            $Com->ObjFun->showmessage('服务器出错！');
        }
        $catPropListField = array(
            'Name',
            'Code',
        );
        $catPropList = M('jy_prop_list')
                       ->field($catPropListField)
                       ->select();
        //渠道
        $catChannelField = array(
            'name',
            'account',
            'id',
        );
        $catChannel = M('jy_admin_users')
            ->where('channel = 2 and isdel = 1')
            ->field($catChannelField)
            ->select();
        $this->assign('catChannel',$catChannel);
        $this->assign('PropList',$catPropList);
        $this->assign('VersionList',$VersionList);
        $this->display('index');
    }

    public  function  send(){

        $Com = D('Com');
        $obj = $Com->ObjFun;
        $msgArr = array(
            2001=>'更新成功！',
            3002=>'与游戏服务器断开，请稍后再试！',
            3003=>'与游戏服务器断开，请稍后再试！',
            4001=>'审核状态不明确',
            4002=>'非法操作。',
            5002=>'系统错误，请稍后再试！',
            0=> "占位符",
            1=>"请求成功",
            2=>"重复创建",
            3=>"数据保存错误",
            4=>"参数错误",
            5=>"服务器逻辑错误",
            6=>"金币不足",
            7=>"没有玩家信息",
            8=>"重复登录",
            9=>"正在进行游戏",
            10=>"没有这个玩家",
            11=>"服务器满载",
            12=>"帐号被封",
            13=>"没有该帐号信息",
            14=>"钻石不足",
            15=>"没有游戏服",
            16=>"该帐号被另一台设备登录",
            17=>"创建次数达到最大",
            18=>"账号名不符合规则",
            19=>"密码不符合规则",
            20=>"操作不合法",
            21=>"账号密码不匹配",
        );
        $result = 2001;
            $Type = I('param.Type','','intval');
            if(empty($Type)){
                $result = 4001;
                goto response;
            }
            //用户ID
            $playerid = I('param.playerid',0,'intval');
            //标题
            $Title    = I('param.Title','','trim');
            //正文
            $Content  = I('param.Content','','trim');
            //砖石
            $Diamond  = I('param.Diamond',0,'intval');
            //金币
            $Gold     = I('param.Gold',0,'intval');
            //道具
            $Prop = I('param.Prop','','trim');
            //卡号
            $CardNum  = I('param.CardNum','','trim');
            //卡密
            $CardPwd  = I('param.CardPwd','','trim');
            //版本号
            $Version  = I('param.Version','','trim');
            //是否添加道具
            $IsGive  = I('param.IsGive',1,'intval');
            //渠道或个人

            $Channel  = I('param.Channel','','trim');

            if($playerid<=0 && $Channel == 1){
                $result =  4002;
                goto  response;
            }

            //已入protobuf类型
            $obj->ProtobufObj(array(
                'Protos/PBS_UsrDataOprater.php',
                'Protos/PBS_UsrDataOpraterReturn.php',
                'Protos/PBS_SendEmail2All.php',
                'Protos/PBS_SendEmail2AllReturn.php',
                'Protos/OptSrc.php',
                'Protos/UsrDataOpt.php',
                'OptReason.php',
                'RPB_PlayerNumerical.php',
                'PB_Email.php',
                'EmailType.php',
                'PB_Item.php',
            ));
            //实话对象
            if($Channel == 1){
                $UsrData        =   new  PBS_UsrDataOprater();
                $UsrDataReturn  =   new  PBS_UsrDataOpraterReturn();
                $OptReason      =   new  \OptReason();
                $OptSrc         =   new  OptSrc();
                $UsrDataOpt     =   new  UsrDataOpt();
                //设置用户
                $UsrData->setPlayerid($playerid);
                //发送者
                $UsrData->setSrc($OptSrc::Src_PHP);
                //发送类型
                $UsrData->setOpt($UsrDataOpt::Modify_Player);
                $UsrData->setReason($OptReason::gm_tool);

            }
            $EmailType      =   new  \EmailType();
            $PB_Email       =   new  \PB_Email();
            //标题
            $PB_Email->setTitle($Title);
            //正文
            $PB_Email->setData($Content);
            if($Type == 1){
                //卡密邮件
                $PB_Email->setType($EmailType::EmailType_Card);
                $PB_Email->setCardNum($CardNum);
                $PB_Email->setCardPwd($CardPwd);
            }elseif ($Type == 2){
                //金币砖石道具

                if($Diamond != 0){
                    $PB_Email->setDiamond($Diamond);
                }
                if($Gold != 0){
                    $PB_Email->setGold($Gold);
                }
                if($IsGive == 2){
                    if(!empty($Prop)){
                        foreach ($Prop as $k=>$v){
                            $Item  =  new \PB_Item();
                            $Item->setNum($v['Num']);
                            $Item->setId($v['Id']);
                            $PB_Email->appendItems($Item);
                        }
                    }
                }

                $PB_Email->setType($EmailType::EmailType_Sys);

            }else{
                //普通文字邮件
                $PB_Email->setType($EmailType::EmailType_Sys);
            }
            if($Channel == 1 ){
                //添加邮件
                $UsrData->setSendEmail($PB_Email);
                //序列化
                $UsrDataString = $UsrData->serializeToString();
                //发送请求
                $Version =  $Com->CatGameVer;


                $Header = array(
                    'PBName:'.'protos.PBS_UsrDataOprater',
                    'PBSize:'.strlen($UsrDataString),
                    'UID:'.$playerid,
                    'PBUrl:'.CONTROLLER_NAME.ACTION_NAME,
                    'Version:'.$Version,
                );
                $Respond =  $obj->ProtobufSend($Header,$UsrDataString);
                if($Respond  == 504){
                    $result = 3003;
                    goto response;
                }
                if(strlen($Respond)==0){
                    $result = 3004;
                    goto response;
                }
                //接受回应
                $UsrDataReturn->parseFromString($Respond);
                $ReplyCode = $UsrDataReturn->getCode();
                //判断结果
                if($ReplyCode != 1){
                    $result = $ReplyCode;
                    goto response;
                }
            }

            if($Channel == 2 || $Channel != 1){

                $PBS_SendEmail2All          =   new  PBS_SendEmail2All();
                $PBS_SendEmail2AllReturn    =   new  PBS_SendEmail2AllReturn();

                $PB_Email->setType($EmailType::EmailType_Sys);
                if($Channel == 2 ){
                    $PBS_SendEmail2All->setChannel('global');
                }else{
                    $PBS_SendEmail2All->setChannel($Channel);
                }
                $PBS_SendEmail2All->setSendEmail($PB_Email);
                $String = $PBS_SendEmail2All->serializeToString();


                $Header = array(
                    'PBName:'.'protos.PBS_SendEmail2All',
                    'PBSize:'.strlen($String),
                    'UID:1',
                    'PBUrl:'.CONTROLLER_NAME.ACTION_NAME,
                    'Version:'.$Version,
                );

                $Respond =  $obj->ProtobufSend($Header,$String);
                if(strlen($Respond)==0){
                    $result = 3003;
                    goto response;
                }
                if($Respond  == 504){
                    $result = 3004;
                    goto response;
                }
                $PBS_SendEmail2AllReturn->parseFromString($Respond);
                $ReplyCode =   $PBS_SendEmail2AllReturn->getCode();
                if($ReplyCode != 1){
                    $result = $ReplyCode;
                    goto response;
                }

            }
            response:
            $response =array(
                'code'=>$result,
                'msg'=>$msgArr[$result],

            );
            echo json_encode($response);
            exit();
    }


}