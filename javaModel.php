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
<form method="get">
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
$savePost="";
$saveFields="";
while($rs=mysqli_fetch_array($res,MYSQLI_ASSOC)){
	 
	  
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
$bTable=str_replace("mod_","",$table);
$aa=explode("_",$bTable);
$nTable="";
foreach($aa as $v){
	$nTable.=ucwords($v);
}	
$tableModel=$nTable."Model";
$str='
package com.deitui.morelang.index.model;

import java.util.List;
import java.util.ArrayList;
import com.alibaba.fastjson.JSONObject;
import com.model.Help;
import com.model.Model;

public class '.$nTable.'Model extends Model {
	public '.$nTable.'Model() {
		this.preTable=this.table_pre+"'.$table.'";
	}
	
	public List Dselect() {
		List list=this.select();
		int len=list.size();
        if(len==0) {
        	return list;
        }
        for(int i=0;i<len;i++){
            Object obj=list.get(i);
            JSONObject json= (JSONObject) JSONObject.toJSON(obj);
            
            json.put("imgurl", Help.image_site(json.get("imgurl")+""));
            String imgsdata=json.get("imgsdata")+"";
            String imgList[]=imgsdata.split(",");
            for(int ii=0;ii<imgList.length;ii++){
                imgList[ii]=Help.image_site(imgList[ii]);
            }
            
            json.put("imgList",imgList);
           
            list.set(i,json);
        }
		return list;
	}
	 public List getListByIds(ArrayList ids,String fields) {
    	if(fields=="") {
			fields="*";
		}
		String idStr=Help._implode(ids); 
		List list=this.fields(fields).where(" '.$pKey.' in("+idStr+") ").Dselect();
		return list;
    }
}


';

echo "<textarea style='width:1024px;height:400px;'>$str</textarea>";
?>
 