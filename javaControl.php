<?php
/**
*Author 雷日锦 362606856@qq.com
*生成控制器文件
*/
require("config.php");
require("nav.php");
 
function getpost($Type,$field){
	if(in_array($field,array("dateline","token","createtime","love_num","fav_num","view_num","comment_num"))){
		return "";
	}
	 if(substr($field,0,2)=="is") return "";
	$str=strtolower(substr($Type,0,strpos($Type,"(")));
	switch($str){
		case "int":
		case "smallint":
		case "tinyint":
		case "mediumint":
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
     目录：
     
      <select name="dir">
     	<?php
			$tpls=array("index","admin","shop");
        	foreach($tpls as $v){
				if($v==$_GET['dir']){
					echo '<option value="'.$v.'" selected>'.$v.'</option>';
				}else{
					echo '<option value="'.$v.'">'.$v.'</option>';
				}
			}
		?>
     </select>
     
    
    用户端 <input type="checkbox" value="1" name="isuser" /> 是  
  </p>
  <p>
  <input name="list" type="checkbox" value="1" /> list
  <input name="show" type="checkbox" '.$pKey.'="" value="1" > show
    <input name="add" type="checkbox" '.$pKey.'="add" value="1" />
    add
   
   <input type="checkbox" name="status" value="1" /> status
    <input name="delete" type="checkbox" '.$pKey.'="delete" value="1" />
    delete
    <input type="checkbox" name="istpl" value="1" />生成模板
    <input name="copy" type="checkbox" value="1">覆盖
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
		$comDir="index";
		break;
	case "admin":
		$dir="/admin";
		$comDir="admin";
		break;
}
	
//主键
$pKey="";
$i=0;
//所有字段
$fields=[];
$savePost="";
$saveFields="";
$fieldKey=[];
$setI=0;
while($rs=mysqli_fetch_array($res,MYSQLI_ASSOC)){
	$fieldKey[]=$rs["Field"]; 
	  
	if($i==0){
		$pKey=$rs['Field'];
		$savePost.=getpost($rs["Type"],$rs["Field"]);
		$saveFields.='indata.put("'.$rs["Field"].'", '.$rs["Field"].');'."\r\n";	
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
//where
$statusWhere='String where="status in(0,1,2) ";';
if(!in_array("status",$fieldKey)){
	$statusWhere='String where=" 1 ";';
}
//imgurl
$dataImgurl="";
$dataTrueImgurl="";
 
if(in_array("imgurl",$fieldKey)){
	$dataImgurl='data.put("imgurl", Help.image_site(data.get("imgurl")+""));';
	$dataTrueImgurl='data.put("trueimgurl", Help.image_site(data.get("imgurl")+""));';
}
$str='
package com.deitui.morelang.'.$mm.'.'.$comDir.';

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.deitui.morelang.'.$mm.'.model.'.$tableModel.';
import com.model.Help;

@RestController
@CrossOrigin("*")
public class '.$nTable.'Controller {
	
	@RequestMapping("'.$dir.'/'.$bTable.'/index")
	public String Index(
		@RequestParam(value="per_page",defaultValue="0") int per_page,
		@RequestParam(value="limit",defaultValue="0") int limit
	) {
		'.$tableModel.' am=new '.$tableModel.'();
		'.$statusWhere.'
		int start=per_page;
		if(limit==0){
			limit=24;
		}
		List list=am.where(where).limit(start,limit).Dselect(); 
		int rscount=Integer.parseInt(am.where(where).fields("count(*)").selectOne());
		per_page=per_page+limit;
		per_page=per_page>rscount?0:per_page;
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("list", list);
		redata.put("rscount", rscount);
        redata.put("per_page", per_page);
		redata.put("limit",limit);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("'.$dir.'/'.$bTable.'/show")
	public String Show(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="'.$pKey.'",defaultValue="0") int '.$pKey.'
	) {
		'.$tableModel.' am=new '.$tableModel.'();
		Map data=am.where("'.$pKey.'="+'.$pKey.').selectRow();
		'.$dataImgurl.'	
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("'.$dir.'/'.$bTable.'/add")
	public String Add(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="'.$pKey.'",defaultValue="0") int '.$pKey.'
	) {
		'.$tableModel.' am=new '.$tableModel.'();
		Map data=new HashMap();
		if('.$pKey.'!=0) {
			data=am.where("'.$pKey.'="+'.$pKey.').selectRow();
			'.$dataTrueImgurl.'
		}
		
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("'.$dir.'/'.$bTable.'/save")
	public String Save(
		@RequestParam(value="token",defaultValue="") String token,
		'.$savePost.'
	) {
		Map indata= new HashMap();
		'.$saveFields.'
		'.$tableModel.' am=new '.$tableModel.'();
		if('.$pKey.'==0) {
			am.insert(indata);
		}else {
			am.update(indata, "'.$pKey.'="+'.$pKey.');
		}
		return Help.success(0, "保存成功");
	}
	
	@RequestMapping("'.$dir.'/'.$bTable.'/status")
	public String Status(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="'.$pKey.'",defaultValue="0") int '.$pKey.'
	) {
		'.$tableModel.' am=new '.$tableModel.'();
		Map row=am.where("'.$pKey.'="+'.$pKey.').selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("status")==1) {
			status=2;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("status", status);
		am.update(indata,"'.$pKey.'="+'.$pKey.');
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("status", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("'.$dir.'/'.$bTable.'/recommend")
	public String recommend(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="'.$pKey.'",defaultValue="0") int '.$pKey.'
	) {
		'.$tableModel.' am=new '.$tableModel.'();
		Map row=am.where("'.$pKey.'="+'.$pKey.').selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("is_recommend")==1) {
			status=0;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("is_recommend", status);
		am.update(indata,"'.$pKey.'="+'.$pKey.');
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("is_recommend", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("'.$dir.'/'.$bTable.'/delete")
	public String delete(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="'.$pKey.'",defaultValue="0") int '.$pKey.'	
	) {
		'.$tableModel.' am=new '.$tableModel.'();
		Map indata= new HashMap();
		indata.put("status", 11);
		am.update(indata, "'.$pKey.'="+'.$pKey.');
		return Help.success(0, "删除成功");
	}
	
	
	
}

';

echo "<textarea style='width:1024px;height:400px;'>$str</textarea>";
?>
 