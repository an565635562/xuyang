<?php
namespace jy_admin\Model;
use Protos\game_numerical_const_gold_pool_level;
use Protos\game_numerical_const_gold_pool_ratio;
use Think\Model;
use Protos\game_numerical;
use Protos\game_numerical_const_boss_rate_params;
use Protos\game_numerical_const_down_grade;
use Protos\game_numerical_const_fish_card_rate;
use Protos\game_numerical_const_key_recharge_effect;
use Protos\game_numerical_const_recharge_effect;
use Protos\game_numerical_const_return_gold_rate;
use Protos\game_numerical_dynamic_gold_pool;
use Protos\PBS_gm_numerical_op;
use Protos\PBS_gm_numerical_op_return;
use Protos\PBS_gm_numerical_require;
use Protos\PBS_gm_numerical_require_return;

class ComModel extends Model{
    protected $autoCheckFields = false;
   /***
   *  查看游戏游戏版本号
   * @param  int  $playerid 用户ID
   */
   public function CatGameVer($playerid){
        $field = array(
            'game_ver'
        );
        $CatData = M('game_account')
                   ->where('playerid = '.$playerid)
                   ->field($field)
                   ->find();
        if(empty($CatData)){
            return false;
        }
        return $CatData['game_ver'];
   }

}

