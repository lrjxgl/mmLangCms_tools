<?php
/*
*Author 雷日锦 362606856@qq.com
*配置文件
*
*/
error_reporting(E_ALL^E_NOTICE);
$localhost="localhost";
$user="root";
$password="root";
$database="mmlang";
$tablepre="sky_";
define("SKINS","themes");
define("ROOT_PATH",dirname(dirname(__FILE__))."/");
header("Content-type:text/html;charset=utf-8");
$link=mysqli_connect($localhost,$user,$password) or die('数据库服务器连接失败');
mysqli_select_db($link,$database) or die("数据库选择失败");
mysqli_query($link,"SET NAMES UTF8");
/*创建文件夹*/
function umkdir($dir)
{
	$arr=explode("/",$dir);
	foreach($arr as $key=>$val)
	{
		$d="";
		for($i=0;$i<=$key;$i++)
		{
			$d.=$arr[$i]."/";
		}
		if(!file_exists($d) && (strpos($val,":")===false))
		{
			mkdir($d,0755);
		}
	}
}

function html($c){
	$c=str_replace("&lt;","<",$c);
	$c=str_replace("&gt;",">",$c);
	return $c;
}
/*驼峰转下划线*/
function Abt($camelCaps,$separator='_')
{
	return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}

?>