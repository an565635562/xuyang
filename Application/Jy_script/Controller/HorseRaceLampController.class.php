<?php
/**
* 跑马灯
*/
namespace Jy_script\Controller;
use Protos\PBS_SysBroadcast;
use Protos\PBS_SysBroadcastReturn;
use Think\Controller;
class HorseRaceLampController extends Controller {
    public function index(){
           // 查询跑马灯信息
        $time = date('Y-m-d H:i:s',time());
        $CatHorseRaceLampField = array(
            'b.account as Channel',
            'a.Content',
            'a.Status',
            'a.Id',
            'a.Version',
        );
        $CatHorseRaceLamp = M('jy_horse_race_lamp as a')
                             ->join('jy_admin_users as b on b.id = a.Channel and b.Isdel = 1','left')
                             ->where('Timing = 2 and Btime > str_to_date("'.$time.'","%Y-%m-%d %H:%i:%s")')
                             ->field($CatHorseRaceLampField)
                             ->order('Sort desc')
                             ->select();





        if(empty($CatHorseRaceLamp)){
               exit();
        }
        $ObjFun = new \Common\Lib\func();
        //已入protobuf 类
        $ObjFun->ProtobufObj(array(
            'Protos/PBS_SysBroadcast.php',
            'Protos/PBS_SysBroadcastReturn.php',
            'PB_PhpBroadcast.php',
        ));
        $SysBroadcast           =   new PBS_SysBroadcast();
        $SysBroadcastReturn     =   new PBS_SysBroadcastReturn();
        $PhpBroadcast           =   new \PB_PhpBroadcast();
        foreach ($CatHorseRaceLamp as $k=>$v){
            $PhpBroadcast->setData($v['Content']);
            if($v['Channel']){
                $SysBroadcast->setChannel($v['Channel']);
            }else{
                $SysBroadcast->setChannel('global');
            }
            $SysBroadcast->setPhpBc($PhpBroadcast);
            $String = $SysBroadcast->serializeToString();

            $Header = array(
                'PBName:'.'protos.PBS_SysBroadcast',
                'PBSize:'.strlen($String),
                'UID:1',
                'PBUrl:'.CONTROLLER_NAME.ACTION_NAME,
                'Version:'.$v['Version'],
            );
            $Respond =  $ObjFun->ProtobufSend($Header,$String);
            if(strlen($Respond)==0){
                return false;
            }
            if($Respond  == 504){
                return false;
            }
            $SysBroadcastReturn->parseFromString($Respond);
            $ReplyCode =   $SysBroadcastReturn->getCode();
            if($ReplyCode != 1){
               exit();
            }else{
//               if($v['Status']  == 1){
//                   $dataHorseRaceLamp = array(
//                       'Status'=>2,
//                   );
//                    $upHorseRaceLamp = M('jy_horse_race_lamp')->where('Id = '.$v['Id'])->save($dataHorseRaceLamp);
//               }
            }
        }
    }

}