<?php
namespace Jy_script\Controller;
use Think\Controller;
use Think\Model;
class TestController extends Controller {
    public function index(){
        $time = strtotime('2018-01-13');
        $StartTime = date('Y-m-d H:i:s',$time);
        $EndTime   =  date('Y-m-d H:i:s',$time+24*60*60);
        $where = 'a.`Status` = 2 and a.`PayChannel` =  "JYHD_HUAWEI" and  a.CallbackTime < str_to_date("'.$EndTime.'","%Y-%m-%d  %H:%i:%s") 
        and  a.CallbackTime >= str_to_date("'.$StartTime.'","%Y-%m-%d  %H:%i:%s")';
        $catData = $this->CatGoods($where);


        $expTitle = "商城购买-2018-01-13";

        $expCellName = array(
            'Id',
            '商品',
            '购买人数',
            '购买次数',
            '总价',
        );
        $this->exportExcel($expTitle,$expCellName,$catData,25);
        print_r($catData);
    }

    public function CatGoods($where){
        $goods = M('jy_goods_all')->where('IsDel = 1 and ShowType = 1 or Code in (7,11,12,13)')->field(array(
            'Id',
            'Name',
        ))->select();
        $mall  = M('jy_users_order_info as a')
            ->join('jy_users_order_goods as b on  a.`playerid` =  b.`playerid` 
                        and a.`PlatformOrder` = b.`PlatformOrder` 
                        and b.`IsGive` = 1')
            ->where($where)
            ->field(array(
                'b.`GoodsID` as GoodsID',
                'a.`OrderName`',
                'count(distinct a.`playerid`) UserPay',
                'count(b.`GoodsID`) as ShopNum',
                'sum(a.`Price`) as Price',
            ))
            ->group('GoodsID')
            ->select();
        foreach ($mall as $k=>$v) $mallSort[$v['GoodsID']] = $v;
        foreach ($goods as $k=>$v){
            if($mallSort[$v['Id']]){
                $goods[$k]['ShopNum'] = $mallSort[$v['Id']]['ShopNum'];
                $goods[$k]['UserPay'] = $mallSort[$v['Id']]['UserPay'];
                $goods[$k]['Price'] = $mallSort[$v['Id']]['Price'];
            }else{
                $goods[$k]['ShopNum'] = 0;
                $goods[$k]['UserPay'] = 0;
                $goods[$k]['Price'] = 0;
            }
        }
        return $goods;
    }






    public function exportExcel($expTitle,$expCellName,$expTableData,$setWidth = 20){
        include JY_ROOT."PHPExcel/PHPExcel.php";
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
        //实例对象
        $PHPExcel = new \PHPExcel();
        //创建工作区
        $PHPExcel->createSheet(0);
        // 设置当前激活的工作表编号
        $PHPExcel->setActiveSheetIndex(0);
        // 获取当前激活的工作表
        $Sheet = $PHPExcel->getActiveSheet();
        //居中
        $PHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $PHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //设置背景样色
        $PHPExcel-> getActiveSheet()->getStyle( 'A1:T1')-> getFill() -> setFillType(\PHPExcel_Style_Fill :: FILL_SOLID);
        $PHPExcel-> getActiveSheet() -> getStyle('A1:T1') -> getFill() -> getStartColor() -> setARGB('#abd4e8');
        foreach ($cellName as $k=>$v){
            $Sheet->getColumnDimension($v)->setWidth(20);
            $Sheet->setCellValue($v.'1',$expCellName[$k]);
        }
        foreach ($expTableData as $k=>$v){
            $i = $k+2;
            $j = 0;
            foreach ($v as $key=>$val){
                $Sheet->setCellValue($cellName[$j].$i,$val);
                $j++;
            }
        }
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$expTitle.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }



}