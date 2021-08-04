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
			return '@RequestParam(value="'.$field.'",defaultValue="0") int '.$field.'';
		break;
		
		case "decimal":
			return '@RequestParam(value="'.$field.'",defaultValue="0") Double '.$field.'';
		default:
			return '@RequestParam(value="'.$field.'",defaultValue="") String '.$field.'';
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
while($rs=mysqli_fetch_array($res,MYSQLI_ASSOC)){
	$fieldKey[]=$rs["Field"];  
	  
	if($i==0){
		$pKey=$rs['Field'];
		$savePost.=getpost($rs["Type"],$rs["Field"]); 
		$i++;
	}else{
		$fields[]=$rs;
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
$createTime='const CREATED_AT= null;';
$updateTime='const UPDATED_AT= null;';
if(in_array("createtime",$fieldKey)){
	$createtime='protected $created_at="createtime"; ';
}
if(in_array("updateTime",$fieldKey)){
	$updateTime='protected $updated_at="updatetime"; ';
}
$str='
<?php
namespace app\\'.$mm.'\model;
use support\Model;
use ext\Help;
use ext\DBS; 
class '.$tableModel.' extends Model{
	 
	protected $table="'.$table.'";
	protected $primaryKey = "'.$pKey.'";
	'.$createTime.'
	'.$updateTime.'  
	public  function Dselect($list){
		if(empty($list)) return $list;
		foreach($list as &$v){
			if(isset($v->imgurl)){
				$v->imgurl=Help::images_site($v->imgurl);
			}
		}
		return $list; 
	}
	public function getListByIds($ids,$fields="*"){
		if(empty($ids)) return [];
		$list=$this->whereIn("'.$pKey.'",$ids)->selectRaw($fields)->get();
		$list=$this->Dselect($list);
		$reList=[];
		if($list){
			foreach($list as $v){
				$reList[$v->'.$pKey.']=$v;
			}
		}
		return $reList;
	}
	
}
';

echo "<textarea style='width:1024px;height:400px;'>$str</textarea>";
?>
 