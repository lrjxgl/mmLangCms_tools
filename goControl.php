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
//package
$package=$mm.ucwords($dir);
$mmModel=$mm."Model"; 

//where
$statusWhere='where:=" status in(0,1,2) ";';
$andStatus=" AND status=1 ";
$orStatus=" AND status<4 ";
if(!in_array("status",$fieldKey)){
	$statusWhere='where:=" 1 ";';
	$andStatus="";
	$orStatus="";
}

//imgurl
$dataImgurl="";
$dataTrueImgurl="";
$deleteStr='db.Model('.$mmModel.'.'.$tableModel.'{}).Where("'.$pKey.'=?", '.$pKey.').Update("status", 11)'; 
if(in_array("imgurl",$fieldKey)){
	$dataImgurl='data.put("imgurl", Help.image_site(data.get("imgurl")+""));';
	$dataTrueImgurl='data.put("trueimgurl", Help.image_site(data.get("imgurl")+""));';
	$deleteStr='db.Delete('.$mmModel.'.'.$tableModel.'{},'.$pKey.')'; 
}
/**处理*action*/
$isCreateIndex=true;
$isCreateList=true;
$isCreateShow=true;
$isCreateMy=true;
$isCreateAdd=true;
$isCreateSave=true;
$isCreateStatus=true;
if(!in_array("status",$fieldKey)) $isCreateStatus=false;
$isCreateRecommend=true;
if(!in_array("isrecommend",$fieldKey)) $isCreateRecommend=false;
$isCreateDelete=true;
$checkAccess='';
$userDataAccess="";
switch($_GET["dir"]){
	case "index":
		$isCreateRecommend=false;
		$checkAccess='
			userid := access.UserCheckAccess(c)
			if userid == 0 {
				return config.Success(c, 1000, "请先登录")
			}
		';
		if(!in_array("userid",$fieldKey)){
			$isCreateAdd=false;
			$isCreateMy=false;
			$isCreateAdd=false;
			$isCreateSave=false;
			$isCreateStatus=false;
			$isCreateDelete=false;
		}
		$userDataAccess='
			if(data.Userid!=userid){
				return config.Success(c,0,"暂无权限");
			}
		'."\r\n";
		break;
	case "admin":
		$isCreateList=false;
		$isCreateShow=false;
		$isCreateMy=false;
		
		$checkAccess='adminid := access.AdminCheckAccess(c)
	if adminid == 0 {
		return config.Success(c, 1000, "暂无权限")
	}'."\r\n";
		break;
}
//end Action
$str='
package '.$package.'

import (
	"app/config"
	"app/access"
	"app/'.$mm.'/'.$mm.'Model"
	"fmt"
	"net/http"
	"reflect"
	"strconv"
	"time"

	"github.com/labstack/echo/v4"
)
/*解决import未使用*/
func '.$nTable.'Null(c echo.Context) (err error){
	 
	now := time.Now()
	'.$checkAccess.'
	reJson := make(map[string]interface{})
	reJson["error"] = 0
	reJson["message"] = "success"
	reJson["now"]=now;
	 
	return c.JSON(http.StatusOK, reJson)
}

/*@@'.$nTable.'Index@@*/
func '.$nTable.'Index(c echo.Context) (err error) {
	fmt.Print("forumIndex")
	'.($dir=='index'?'':$checkAccess).'
	var db = config.Db
	var list = []'.$mmModel.'.'.$tableModel.'{}
	 
	'.$statusWhere.'
	//统计数量
	start, err := strconv.Atoi(c.QueryParam("per_page"))
	if err!=nil {
		start=0;
	}
	limit, err2 := strconv.Atoi(c.QueryParam("limit"))
	if err2!=nil || limit==0 {
		limit=24;
	}
	var rscount int64;
	db.Model(&'.$mmModel.'.'.$tableModel.'{}).Where(where).Count(&rscount);
	//获取列表
	res := db.Where(where).Limit(limit).Offset(start).Find(&list)
	if res.Error != nil {
		list = []'.$mmModel.'.'.$tableModel.'{}
	}
	//输出浏览器
	var per_page int64=int64(start+limit);
	if per_page>rscount {
		per_page=0;
	}
	reJson := make(map[string]interface{})
	reJson["error"] = 0
	reJson["message"] = "success"
	reJson["list"] = '.$mmModel.'.'.$nTable.'List(list)
	reJson["type"] = reflect.TypeOf(list)
	reJson["rscount"]=rscount;
	reJson["per_page"]=per_page;
	return c.JSON(http.StatusOK, reJson)
}
';

$isCreateList &&  $str.='
/*@@'.$nTable.'List@@*/
func '.$nTable.'List(c echo.Context) (err error) {
	fmt.Print("forumIndex")
	'.($dir=='index'?'':$checkAccess).'
	var db = config.Db
	var list = []'.$mmModel.'.'.$tableModel.'{}
	 
	'.$statusWhere.'
	//统计数量
	start, err := strconv.Atoi(c.QueryParam("per_page"))
	if err!=nil {
		start=0;
	}
	limit, err2 := strconv.Atoi(c.QueryParam("limit"))
	if err2!=nil || limit==0 {
		limit=24;
	}
	var rscount int64;
	db.Model(&'.$mmModel.'.'.$tableModel.'{}).Where(where).Count(&rscount);
	//获取列表
	res := db.Where(where).Limit(limit).Offset(start).Find(&list)
	if res.Error != nil {
		list = []'.$mmModel.'.'.$tableModel.'{}
	}
	//输出浏览器
	var per_page int64=int64(start+limit);
	if per_page>rscount {
		per_page=0;
	}
	reJson := make(map[string]interface{})
	reJson["error"] = 0
	reJson["message"] = "success"
	reJson["list"] = '.$mmModel.'.'.$nTable.'List(list)
	reJson["type"] = reflect.TypeOf(list)
	reJson["rscount"]=rscount;
	reJson["per_page"]=per_page;
	return c.JSON(http.StatusOK, reJson)
}
';


$isCreateShow &&  $str.='
/*@@'.$nTable.'Show@@*/
func '.$nTable.'Show(c echo.Context) (err error) {
	'.($dir=='index'?'':$checkAccess).'
	'.$pKey.' := c.QueryParam("'.$pKey.'")
	var db = config.Db
	data := new('.$mmModel.'.'.$tableModel.')
	res := db.Where("'.$pKey.'=? '.$andStatus.' ", '.$pKey.').First(&data)
	if res.Error != nil {
		return config.Success(c, 1, "数据不存在")
	}
	//输出浏览器
	reJson := make(map[string]interface{})
	reJson["error"] = 0
	reJson["message"] = "success"
	reJson["data"] = data
	return c.JSON(http.StatusOK, reJson)
}
';

$isCreateMy &&  $str.='
/*@@'.$nTable.'My@@*/
func '.$nTable.'My(c echo.Context) (err error) {
	fmt.Print("forumIndex")
	'.($dir=='index'?'':$checkAccess).'
	var db = config.Db
	var list = []'.$mmModel.'.'.$tableModel.'{}
	 
	'.$statusWhere.'
	//统计数量
	start, err := strconv.Atoi(c.QueryParam("per_page"))
	if err!=nil {
		start=0;
	}
	limit, err2 := strconv.Atoi(c.QueryParam("limit"))
	if err2!=nil || limit==0 {
		limit=24;
	}
	var rscount int64;
	db.Model(&'.$mmModel.'.'.$tableModel.'{}).Where(where).Count(&rscount);
	//获取列表
	res := db.Where(where).Limit(limit).Offset(start).Find(&list)
	if res.Error != nil {
		list = []'.$mmModel.'.'.$tableModel.'{}
	}
	//输出浏览器
	var per_page int64=int64(start+limit);
	if per_page>rscount {
		per_page=0;
	}
	reJson := make(map[string]interface{})
	reJson["error"] = 0
	reJson["message"] = "success"
	reJson["list"] = '.$mmModel.'.'.$nTable.'List(list)
	reJson["type"] = reflect.TypeOf(list)
	reJson["rscount"]=rscount;
	reJson["per_page"]=per_page;
	return c.JSON(http.StatusOK, reJson)
}
';

$isCreateAdd &&  $str.='
/*@@'.$nTable.'Add@@*/
func '.$nTable.'Add(c echo.Context) (err error) {
	'.$checkAccess.'
	'.$pKey.', err := strconv.Atoi(c.QueryParam("'.$pKey.'"))
	var db = config.Db

	var data = '.$mmModel.'.'.$tableModel.'{}
	if '.$pKey.' != 0 {
		res := db.Where("'.$pKey.'=? '.$orStatus.' ", '.$pKey.').First(&data)
		if res.Error != nil {
			return config.Success(c, 1, "数据不存在")
		}
		'.$userDataAccess.'
	}

	//输出浏览器
	reJson := make(map[string]interface{})
	reJson["error"] = 0
	reJson["message"] = "success"
	reJson["data"] = data
	reJson["'.$pKey.'"] = '.$pKey.'
	return c.JSON(http.StatusOK, reJson)
}
';
$isCreateSave && $str.='
/*@@'.$nTable.'Save@@*/
func '.$nTable.'Save(c echo.Context) (err error) {
	'.$checkAccess.'
	'.$pKey.', err := strconv.Atoi(c.FormValue("'.$pKey.'"))
	var db = config.Db
	var data = '.$mmModel.'.'.$tableModel.'{}
	if '.$pKey.' != 0 {
		res := db.Where("'.$pKey.'=? '.$orStatus.' ", '.$pKey.').First(&data)
		if res.Error != nil {
			return config.Success(c, 1, "数据不存在")
		}
		'.$userDataAccess.'
	}
	//新增数据

	postData := map[string]interface{}{}
	postData["title"] = c.FormValue("title")
	postData["description"] = c.FormValue("description")
	now := time.Now()
	postData["createtime"] = now.Format("2006-01-02 15:04:05")
	if '.$pKey.' != 0 {
		db.Create(postData)
	} else {
		db.Model('.$mmModel.'.'.$tableModel.'{}).Where("'.$pKey.'=?", '.$pKey.').Updates(postData)
	}

	//输出浏览器
	reJson := make(map[string]interface{})
	reJson["error"] = 0
	reJson["message"] = "success"
	reJson["data"] = postData
	return c.JSON(http.StatusOK, reJson)
}';

$isCreateStatus && $str.='
/*@@'.$nTable.'Status@@*/
func '.$nTable.'Status(c echo.Context) (err error) {
	'.$checkAccess.'
	'.$pKey.' := c.QueryParam("'.$pKey.'")
	var db = config.Db
	data := new('.$mmModel.'.'.$tableModel.')
	res := db.Where("'.$pKey.'=?", '.$pKey.').First(&data)
	if res.Error != nil {
		return config.Success(c, 1, "数据不存在")
	}
	'.$userDataAccess.'
	status := 1
	if data.Status == 1 {
		status = 2
	}
	db.Model('.$mmModel.'.'.$tableModel.'{}).Where("'.$pKey.'=?", '.$pKey.').Update("status", status)
	reJson := make(map[string]interface{})
	reJson["error"] = 0
	reJson["message"] = "success"
	reJson["status"] = status
	return c.JSON(http.StatusOK, reJson)

}
';

$isCreateRecommend   && $str.='
/*@@'.$nTable.'Recommend@@*/
func '.$nTable.'Recommend(c echo.Context) (err error) {
	'.$checkAccess.'
	'.$pKey.' := c.QueryParam("'.$pKey.'")
	var db = config.Db
	data := new('.$mmModel.'.'.$tableModel.')
	res := db.Where("'.$pKey.'=?", '.$pKey.').First(&data)
	if res.Error != nil {
		return config.Success(c, 1, "数据不存在")
	}
	'.$userDataAccess.'
	isrecommed := 1
	if data.Isrecommend == 1 {
		isrecommed = 0
	}
	db.Model('.$mmModel.'.'.$tableModel.'{}).Where("'.$pKey.'=?", '.$pKey.').Update("isrecommend", isrecommed)

	reJson := make(map[string]interface{})
	reJson["error"] = 0
	reJson["message"] = "success"
	reJson["isrecommend"] = isrecommed
	return c.JSON(http.StatusOK, reJson)

}';

$isCreateDelete && $str.='
/*@@'.$nTable.'Delete@@*/
func '.$nTable.'Delete(c echo.Context) (err error) {
	'.$checkAccess.'
	'.$pKey.' := c.QueryParam("'.$pKey.'")
	var db = config.Db
	data := new('.$mmModel.'.'.$tableModel.')
	res := db.Where("'.$pKey.'=?", '.$pKey.').First(&data)
	if res.Error != nil {
		return config.Success(c, 1, "数据不存在")
	}
	'.$userDataAccess.'
	db.Model('.$mmModel.'.'.$tableModel.'{}).Where("'.$pKey.'=?", '.$pKey.').Update("status", 11)
	return config.Success(c, 0, "删除成功")

}
';

echo "<textarea style='width:1024px;height:400px;'>$str</textarea>";
?>
 