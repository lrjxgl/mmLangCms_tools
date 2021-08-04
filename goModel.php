<?php
/**
*Author 雷日锦 362606856@qq.com
*生成控制器文件
*/
require("config.php");
require("nav.php");
 
function getpost($Type,$field){
	if(in_array($field,array("dateline","userid","createtime","love_num","fav_num","view_num","comment_num"))){
		return "";
	}
	 if(substr($field,0,2)=="is") return "";
	$str=strtolower(substr($Type,0,strpos($Type,"(")));
	switch($str){
		case "int":
		case "smallint":
		case "tinyint":
		case "bigint":
		case "mediumint":
			return '@RequestParam(value="'.$field.'",defaultValue="0") int '.$field.'';
		break;
		
		case "decimal":
			return '@RequestParam(value="'.$field.'",defaultValue="0") Double '.$field.'';
		default:
			return '@RequestParam(value="'.$field.'",defaultValue="") String '.$field.'';
		break;
	}
} 
function parseField($Type){
	$str=strtolower(substr($Type,0,strpos($Type,"(")));
	switch($str){
		case "int":
		case "smallint":
		case "tinyint":
		case "bigint":
		case "mediumint":
			return "uint";
			break;
		case "decimal":
			return "float64";
			break;
		case "datetime":
			return "time.Time";
			break;
		default:
			return "string";
			break;
	}
		
}
 
?>
<form method="get" >
  <p> 
   
    表：<input type="text" name="table" value="<?php echo  $_GET['table'];?>" />
      
    <input type="submit" value="生成" />
  </p>
</form>
<?php
$c=trim($_GET['c']);
$model=trim($_GET['model']);
$table=trim($_GET['table']);
$dir=trim($_GET['dir']);
$tpldir=str_replace($dir."_","",$c);
$tpl=trim($_GET['tpl']);
$istpl=intval($_GET['istpl']);
$list=intval($_GET['list']);
$show=intval($_GET['show']);
$add=intval($_GET['add']);
$status=intval($_GET['status']);
$delete=intval($_GET['delete']);
$copy=intval($_GET['copy']);
$isuser=intval($_GET["isuser"]);
if($tpl=="wap.tpl"){
	$iswap=1;
}else{
	$iswap=0;
}
if(empty($table)) exit("请输入表");
$res=mysqli_query($link,"show columns from ".$tablepre.$table);
$insert_str="\r\n";
//处理目录
switch($dir){
	case "index":
		$dir="";
		break;
	case "admin":
		$dir="/admin";
		break;
}
	
//主键
$pKey="";
$i=0;
//所有字段
$fields=[];
$fieldKey=[];
$savePost="";
$saveFields="";
$createJson="";
while($rs=mysqli_fetch_array($res,MYSQLI_ASSOC)){
	$fieldKey[]=$rs["Field"];  
	  
	if($i==0){
		$pKey=$rs['Field'];
		$savePost.=getpost($rs["Type"],$rs["Field"]); 
		$createJson.=ucwords($rs["Field"]).'      '.parseField($rs["Type"]).'   `gorm:"primaryKey";json:"'.$rs['Field'].'"`'."\r\n";
		$i++;
	}else{
		$fields[]=$rs;
		$createJson.='	'.ucwords($rs["Field"]).'      '.parseField($rs["Type"]).'   `json:"'.$rs['Field'].'"` '."\r\n";
		$p=getpost($rs["Type"],$rs["Field"]);
		if($p!=''){
			$savePost.=",\r\n";
			$savePost.= $p;
			$saveFields.='indata.put("'.$rs["Field"].'", '.$rs["Field"].');'."\r\n";
		}
		
	}
}
 
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
$tableModel=$nTable."Model";
$imgurl="";
$imgs=array("imgurl","user_head","logo","uhead");
foreach($imgs as $img){
	if(in_array($img,$fieldKey)){
		$imgurl='m.'.ucwords($img).' = config.Image_site(m.'.ucwords($img).')';
		break;
	}
}
 
$str='
package '.$mm.'Model

import (
	"app/config"
)

type '.$tableModel.' struct {
	'.$createJson.'
}

func ('.$tableModel.') TableName() string {
	return config.TablePre + "'.$table.'"
}

func '.$nTable.'List(list []'.$tableModel.') []'.$tableModel.' {
	slen := len(list)
	if slen == 0 {
		return list
	}

	for i := 0; i < slen; i++ {
		m := list[i]
		'.$imgurl.'
		list[i] = m
	}
	return list
}

';

echo "<textarea style='width:1024px;height:400px;'>$str</textarea>";
?>
 