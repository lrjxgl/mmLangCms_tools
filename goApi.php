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
$unTables=["sky_navbar","sky_login","sky_user"];
foreach($list as $v){
	if(in_array(trim($v[0]),$unTables)) continue;
	$tables[]=$v[0];
}
 
//javaRoot;
$javaRoot="golang/"; 
if(!isset($_GET["route"])){
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
}
/**/
//生成路由信息
$mds=["index"];
foreach($tables as $table):
	$table=str_replace("sky_","",$table);
	$bTable=str_replace("mod_","",$table);
	$mod=substr($table,0,4);
	if($mod=="mod_"){
		$aa=explode("_",$bTable);
		$mm=$aa[0];
		if(!in_array($mm,$mds)){
			$mds[]=$mm;
		}
	}
endforeach;
foreach($mds as $md){
	writeRoute($md,"index");
	writeRoute($md,"admin");
}
 
echo "项目生成完毕";
function writeCtrl($table,$dir){
	global $mm,$javaRoot,$nTable;
	$host="http://".$_SERVER["HTTP_HOST"];
	$url=$host."/goControl.php?table={$table}&dir={$dir}";	 
	$c=curl_get_contents($url);
	 
	preg_match("/<textarea[^>]*>(.*)<\/textarea>/iUs",$c,$a);
	$content=$a[1];
	$fDir=$javaRoot.$mm."/".$mm.ucwords($dir);
	$file=$fDir.'/'.$nTable.".ctrl.go";
	umkdir($fDir);
	file_put_contents($file,trim($content));
}

function writeModel($table){
	global $mm,$javaRoot,$nTable;
	$tableModel=$nTable."Model";
	$host="http://".$_SERVER["HTTP_HOST"];
	$url=$host."/goModel.php?table={$table}";	 
	$c=curl_get_contents($url);
	 
	preg_match("/<textarea[^>]*>(.*)<\/textarea>/iUs",$c,$a);
	$content=$a[1];
	$fDir=$javaRoot.$mm."/".$mm."model";
	$file=$fDir.'/'.$nTable.".model.go";
	umkdir($fDir);
	file_put_contents($file,trim($content));
}

//生成route
function writeRoute($mm,$dir){
	global $javaRoot;
	$fDir=$javaRoot.$mm."/".$mm.ucwords($dir);
	$files=glob($fDir."/*.go");
	$routeStr="";
	foreach($files as $file){
		$ex=explode(".",basename($file));
		$fname=$ex[0];
		$routeStr.="		/*".$fname."*/\r\n";
		$fc=file_get_contents($file);
		preg_match_all("/@@(\w+)@@/iUs",$fc,$arr);
		if($dir=='index'){
			 
			foreach($arr[1] as $action){
				$abstr=Abt($action);
				$ex=explode("_",$abstr);
				//动作 index show
				$act=$ex[count($ex)-1];
				//ctrl
				$ctrl=str_replace("_".$act,"",$abstr);
				$rootPath='/'.$ctrl."/".$act;
								
				$routeStr.='		e.GET("'.$rootPath.'", '.$mm.'Index.'.$action.');'."\r\n";
			}
			 
		}else{
			
			foreach($arr[1] as $action){
				$abstr=Abt($action);
				$ex=explode("_",$abstr);
				//动作 index show
				$act=$ex[count($ex)-1];
				//ctrl
				$ctrl=str_replace("_".$act,"",$abstr);
				if($mm=="index"){
					$rootPath="/".$dir."/".$ctrl."/".$act;
				}else{
					$rootPath='/'.$dir.'/'.$ctrl."/".$act;
				}	
				$routeStr.='		e.GET("'.$rootPath.'", '.$mm.'Admin.'.$action.');'."\r\n";
			}
		}
	}
 
	$content='
	package router

	import (
		"app/'.$mm.'/'.$mm.ucwords($dir).'"
		"github.com/labstack/echo/v4"
	)

	func '.ucwords($mm).ucwords($dir).'Router(e *echo.Echo) {
'.$routeStr.'
	}

	';
	$fDir=$javaRoot."router";
	$file=$fDir.'/'.$mm.".".$dir.".go";
	umkdir($fDir);
	file_put_contents($file,$content);
}

?>