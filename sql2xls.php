#!/usr/bin/php
<?php 

if($argc != 5){
echo "Usage:  ./sql2exl.php  host user password dbname ";
exit;
}else{

echo "mysql:host={$argv[1]};dbname={$argv[4]}";
}

set_time_limit(30000);//脚本最大执行时间
ini_set('memory_limit', '3072M');
date_default_timezone_set("PRC");
require_once 'phpexcel/Classes/PHPExcel.php';
require_once 'phpexcel/Classes/PHPExcel/IOFactory.php';
require_once 'phpexcel/Classes/PHPExcel/Reader/Excel5.php';

$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load("templates/template1.xls"); 
$sheet = $objPHPExcel->getSheet(0);
$highestRow = $sheet->getHighestRow(); // 取得总行数
$highestColumn = $sheet->getHighestColumn(); // 取得总列数
$objTitle=$objPHPExcel->getActiveSheet()->getTitle();
$excel = new PHPExcel();
$excel->setActiveSheetIndex(0);
$excel->getActiveSheet()->setTitle($objTitle);
$key_arr;
$sql;

for($i=ord('A'); $i <= ord($highestColumn); $i++){
$title=$objPHPExcel->getActiveSheet()->getCell(chr($i)."1")->getValue();
$key=$objPHPExcel->getActiveSheet()->getCell(chr($i)."2")->getValue();

if( $title == "sql" ){
	$sql=$key;
	break;
}

 
$excel->getActiveSheet()->setCellValue(chr($i)."1", $title);
$excel->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);
$key_arr[$i]=$key;
}
print_r($key_arr);
if(!$sql){
exit;
}

$dbh = new PDO("mysql:host={$argv[1]};dbname={$argv[4]}", $argv[2], $argv[3], array(PDO::ATTR_PERSISTENT=>false, 
								  PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES 'utf8';"));


echo "---------------------------------------------\n";
$ret=$dbh->query($sql);

if(!$ret){
echo "error \n";
}
$retarr=$ret->fetchAll(PDO::FETCH_NAMED);

if(empty($retarr)){
echo "null \n";

}else{
$tmp=2;
$len=count($retarr);
print_r($key_arr);
echo "highestColumn:".$highestColumn;
for($i=0;$i< $len; $i++ ){
$message="";
	for($j=ord('A'); $j < ord($highestColumn); $j++){
		$key=$key_arr[$j];
		$excel->getActiveSheet()->setCellValueExplicit(chr($j).($i+2),(!$key ? "" : $retarr[$i][$key]),PHPExcel_Cell_DataType::TYPE_STRING);
		$message.= (!$key ? "" : $retarr[$i][$key])."  ";
	}

echo $message."\t (".($i+1)."/{$len})\n";


}

$write = new PHPExcel_Writer_Excel5($excel);
$filename=date("YmdHis").".xls";
$write->save($filename);


echo "================================================================\n\n\n";
echo "output in ".dirname(__FILE__)."/{$filename} \n\n\n";
echo "================================================================\n";
}
