<?php
set_time_limit(0);
require("config.php");
function curl_get_contents($url,$timeout=10,$referer="http://www.qq.com"){
	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_HEADER, 0);
	 curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	 curl_setopt($ch, CURLOPT_REFERER,$referer); //伪造来路页面 防止被禁止
	 $content= curl_exec($ch);
	 curl_close($ch);
	 return $content;
} 
$res=mysqli_query($link,"show tables");
$list=mysqli_fetch_all($res);
$tables=[];
//无需自动增加的表
$unTables=[
	"sky_admin","sky_admin_group",
	"sky_config",
	"sky_permission",
	"sky_navbar","sky_login","sky_ad","sky_user","sky_pm","sky_dataapi","sky_fav","sky_love","sky_follow"
	,"sky_attach","sky_site","sky_config","sky_article","sky_district"
	,"sky_mod_forum_comment","sky_mod_forum_tags","sky_mod_forum"
];
foreach($list as $v){
	if(in_array(trim($v[0]),$unTables)) continue;
	$tables[]=$v[0];
}
//javaRoot;
$javaRoot="php/"; 
foreach($tables as $table):
	//$table=$tables[0];
	$table=str_replace("sky_","",$table);
	//将下划线转驼峰
	$isModule=0;
	$mm="index";
	$mod=substr($table,0,4);
	if($mod=="mod_"){
		$isModule=1;
	}	
	$bTable=str_replace("mod_","",$table);
	$aa=explode("_",$bTable);
	if($isModule){
		$mm=$aa[0];
	}
	$nTable="";
	foreach($aa as $v){
		$nTable.=ucwords($v);
	}	

	//生成控制器
	//$dir="admin";
	writeCtrl($table,"admin");
	writeCtrl($table,"index");
	//生成模型
	writeModel($table);
endforeach;
echo "success";

function writeCtrl($table,$dir){
	global $mm,$javaRoot,$nTable;
	$host="http://".$_SERVER["HTTP_HOST"];
	$url=$host."/phpControl.php?table={$table}&dir={$dir}";	 
	$c=curl_get_contents($url);
	 
	preg_match("/<textarea[^>]*>(.*)<\/textarea>/iUs",$c,$a);
	$content=$a[1];
	$fDir=$javaRoot.$mm."/".$dir;
	$file=$fDir.'/'.$nTable.".php";
	umkdir($fDir);
	file_put_contents($file,trim($content));
}

function writeModel($table){
	global $mm,$javaRoot,$nTable;
	$tableModel=$nTable."Model";
	$host="http://".$_SERVER["HTTP_HOST"];
	$url=$host."/phpModel.php?table={$table}";	 
	$c=curl_get_contents($url);
	 
	preg_match("/<textarea[^>]*>(.*)<\/textarea>/iUs",$c,$a);
	$content=$a[1];
	$fDir=$javaRoot.$mm."/model";
	$file=$fDir.'/'.$nTable."Model.php";
	umkdir($fDir);
	file_put_contents($file,trim($content));
}

?>