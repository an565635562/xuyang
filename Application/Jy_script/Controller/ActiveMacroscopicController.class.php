<?php
/***
*  活跃分析-宏观数据
*/
namespace Jy_script\Controller;
use Think\Controller;
class ActiveMacroscopicController extends Controller {
    public function index(){
        $time       =  strtotime(date('Y-m-d',time()));
        //$time       =  strtotime('2017-10-24');
        $DaySecond  = 24*60*60;
        $StartTime  =  date('Y-m-d H:i:s',$time-$DaySecond);
        $EndTime    =  date('Y-m-d H:i:s',$time);
        //渠道
        $ChannelData = M('jy_admin_users')
                       ->where('channel = 2 and  isdel = 1')
                       ->field('account')
                       ->select();
        if(empty($ChannelData)){
              exit('无渠道');
        }
        foreach ($ChannelData as $k=>$v) $ChannelDataSort[] = '"'.$v['account'].'"';
        $Channel = implode(',',$ChannelDataSort);
        //账号活跃
        $AccountField = array(
            'login_channel as GroupChannel',
            'count(distinct a.playerid) as Account',
            'count(distinct b.playerid) as PayNum',
            'a.game_ver as VerSion',
        );
        $Account = M('game_login_action as a')
                   ->join('jy_users_order_info as b on  a.playerid = b.playerid  and b.`Status` = 2  
                   and b.CallbackTime < str_to_date("'.$EndTime.'","%Y-%m-%d %H:%i:%s") 
                   and b.CallbackTime >= str_to_date("'.$StartTime.'","%Y-%m-%d %H:%i:%s")','left')
                   ->where('a.login_channel in('.$Channel.') 
                            and  a.login_time < str_to_date("'.$EndTime.'","%Y-%m-%d %H:%i:%s") and 
                            a.login_time >= str_to_date("'.$StartTime.'","%Y-%m-%d %H:%i:%s")')
                   ->field($AccountField)
                    ->group('GroupChannel,VerSion')
                   ->select();

        //设备活跃 安卓

        $EquipmentAndroidField = array(
            'login_channel as GroupChannel',
            'count(distinct concat(mac,imei,imsi)) as android',
            'game_ver as VerSion',
        );
        $EquipmentAndroid = M('game_login_action')
                     ->where('login_channel in('.$Channel.') 
                            and  os_type = 2
                            and  login_time < str_to_date("'.$EndTime.'","%Y-%m-%d %H:%i:%s") and 
                            login_time >= str_to_date("'.$StartTime.'","%Y-%m-%d %H:%i:%s")')
                     ->field($EquipmentAndroidField)
                     ->group('GroupChannel,VerSion')
                     ->select();
        //设备活跃 苹果
        $EquipmentIosField = array(
            'login_channel as GroupChannel',
            'count(distinct uuid) as ios',
            'game_ver as VerSion',
        );
        $EquipmentIos= M('game_login_action')
            ->where('login_channel in('.$Channel.') 
                            and  os_type = 1
                            and  login_time < str_to_date("'.$EndTime.'","%Y-%m-%d %H:%i:%s") and 
                            login_time >= str_to_date("'.$StartTime.'","%Y-%m-%d %H:%i:%s")')
            ->field($EquipmentIosField)
            ->group('GroupChannel,VerSion')
            ->select();
        //周活跃
        $WAUStartTime  = date('Y-m-d H:i:s',$time-$DaySecond*7);
        $WAUField = array(
            'login_channel as GroupChannel',
            'count(distinct playerid) as WAU',
            'game_ver as VerSion',
        );


        $WAU = M('game_login_action')
            ->where('login_channel in('.$Channel.') 
                            and  login_time < str_to_date("'.$EndTime.'","%Y-%m-%d %H:%i:%s") and 
                            login_time >= str_to_date("'.$WAUStartTime.'","%Y-%m-%d %H:%i:%s")')
            ->field($WAUField)
            ->group('GroupChannel,VerSion')
            ->select();


        //月活跃
        $MAUStartTime  = date('Y-m-d H:i:s',$time-$DaySecond*30);
        $MAUField = array(
            'login_channel as GroupChannel',
            'count(distinct playerid) as MAU',
            'game_ver as VerSion',
        );
        $MAU = M('game_login_action')
            ->where('login_channel in('.$Channel.') 
                            and  login_time < str_to_date("'.$EndTime.'","%Y-%m-%d %H:%i:%s") and 
                            login_time >= str_to_date("'.$MAUStartTime.'","%Y-%m-%d %H:%i:%s")')
            ->field($MAUField)
            ->group('GroupChannel,VerSion')
            ->select();
        //用户游戏数
        $UserGameField = array(
            'login_channel  as GroupChannel',
            'count(distinct playerid) as UserGame',
            'game_ver as VerSion',
        );
        $UserGame = M('game_reschange_action')
                    ->where('reason = 15  and   login_channel  in('.$Channel.')   and   opt_time < str_to_date("'.$EndTime.'","%Y-%m-%d %H:%i:%s") 
                            and opt_time >=  str_to_date("'.$StartTime.'","%Y-%m-%d %H:%i:%s")')
                    ->field($UserGameField)
                    ->group('GroupChannel,VerSion')
                    ->select();
        //破产
         $BankruptcyNumField = array(
             'login_channel  as GroupChannel',
             'count(distinct playerid) as BankruptcyNum',
             'count(playerid) as BankruptcyTotal',
             'GROUP_CONCAT(distinct playerid)',
             'game_ver as VerSion'
         );
        $BankruptcyNum   = M('game_broke_action')
                         ->where('login_channel in('.$Channel.')  and  broke_time < str_to_date("'.$EndTime.'","%Y-%m-%d %H:%i:%s") 
                            and broke_time >=  str_to_date("'.$StartTime.'","%Y-%m-%d %H:%i:%s")')
                         ->field($BankruptcyNumField)
                         ->group('GroupChannel,VerSion')
                         ->select();
        //组装数据
        $AccountSort                =   array();
        $UserGameSort               =   array();
        $EquipmentAndroidSort       =   array();
        $EquipmentIosSort           =   array();
        $BankruptcyNumSort          =   array();
        $WAUSort                    =   array();
        $MAUSort                    =   array();
        foreach ($Account as $k=>$v) $AccountSort[$v['GroupChannel'].$v['VerSion']] = $v;
        foreach ($UserGame as $k=>$v) $UserGameSort[$v['GroupChannel'].$v['VerSion']] = $v;
        foreach ($EquipmentAndroid as $k=>$v) $EquipmentAndroidSort[$v['GroupChannel'].$v['VerSion']] = $v;
        foreach ($EquipmentIos as $k=>$v) $EquipmentIosSort[$v['GroupChannel'].$v['VerSion']] = $v;
        foreach ($BankruptcyNum as $k=>$v) $BankruptcyNumSort[$v['GroupChannel'].$v['VerSion']] = $v;
        foreach ($WAU as $k=>$v) $WAUSort[$v['GroupChannel'].$v['VerSion']] = $v;
        foreach ($MAU as $k=>$v) $MAUSort[$v['GroupChannel'].$v['VerSion']] = $v;
        //版本号信息
        $GameVersion = M('jy_game_version')
                       ->field('Version')
                       ->select();
        $ChannelDataSort = array();
        $i = 0;
        foreach ($ChannelData as $k=>$v){
            foreach ($GameVersion as $key=>$val){
                $ChannelDataSort[$i]['account'] = $v['account'].$val['Version'];
                $ChannelDataSort[$i]['Version'] = $val['Version'];
                $ChannelDataSort[$i]['Channel'] = $v['account'];
                $i++;
            }
        }
        $info = array();
        foreach ($ChannelDataSort as $k=>$v){
             //账号活跃
            $info[$k]['VerSion'] = $v['Version'];
            $info[$k]['Channel'] = $v['Channel'];
            if($AccountSort[$v['account']]){
                $info[$k]['Account'] = $AccountSort[$v['account']]['Account'];
                $info[$k]['PayNum'] = $AccountSort[$v['account']]['PayNum'];
            }else{
                $info[$k]['Account']  =  0;
                $info[$k]['PayNum']   =  0;
            }
            //用户游戏数
            if($UserGameSort[$v['account']]){
                $info[$k]['UserGame'] = $UserGameSort[$v['account']]['UserGame'];
            }else{
                $info[$k]['UserGame'] = 0;
            }
            //设备-安卓
            if($EquipmentAndroid[$v['account']]){
                $info[$k]['EquipmentAndroid'] = $EquipmentAndroid[$v['account']]['EquipmentAndroid'];
            }else{
                $info[$k]['EquipmentAndroid'] = 0;
            }
            //设备-苹果
            if($EquipmentIos[$v['account']]){
                $info[$k]['EquipmentIos'] = $EquipmentIos[$v['account']]['ios'];
            }else{
                $info[$k]['EquipmentIos'] = 0;
            }
            //破产
            if($BankruptcyNumSort[$v['account']]){
                $info[$k]['BankruptcyNum'] = $BankruptcyNumSort[$v['account']]['BankruptcyNum'];
                $info[$k]['BankruptcyTotal'] = $BankruptcyNumSort[$v['account']]['BankruptcyTotal'];
            }else{
                $info[$k]['BankruptcyNum'] = 0;
                $info[$k]['BankruptcyTotal'] = 0;
            }
            //周活跃
            if($WAUSort[$v['account']]){
                $info[$k]['WAU'] = $WAUSort[$v['account']]['WAU'];
            }else{
                $info[$k]['WAU'] = 0;
            }
            //月活跃
            if($MAUSort[$v['account']]){
                $info[$k]['MAU'] = $MAUSort[$v['account']]['MAU'];
            }else{
                $info[$k]['MAU'] = 0;
            }
            $info[$k]['DateTime'] = $StartTime;
        }

        $db = M('jy_statistics_activem_acroscopic');
        $addData = $db
                  ->addAll($info);
        exit('结束');

     }
}