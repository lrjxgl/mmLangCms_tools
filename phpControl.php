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
			return '$indata["'.$field.'"]=intval($request->post("'.$field.'","0"));';
		break;
		
		case "decimal":
			return '$indata["'.$field.'"]=floatval($request->post("'.$field.'","0"));';
		default:
			return '$indata["'.$field.'"]=$request->post("'.$field.'","");';
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
		//$savePost.=getpost($rs["Type"],$rs["Field"]);
		//$saveFields.='indata.put("'.$rs["Field"].'", '.$rs["Field"].');'."\r\n";	
		$i++;
	}else{
		$fields[]=$rs;
		
		$p=getpost($rs["Type"],$rs["Field"]);
		 
		if($p!=''){
			$savePost.="\r\n";
			$savePost.= $p;
			//$saveFields.='indata.put("'.$rs["Field"].'", '.$rs["Field"].');'."\r\n";
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
$statusWhere='$where="status in(0,1,2) ";';
if(!in_array("status",$fieldKey)){
	$statusWhere='$where=" 1 ";';
}
//imgurl
$dataImgurl="";
$dataTrueImgurl="";
 
if(in_array("imgurl",$fieldKey)){
	$dataImgurl='data.put("imgurl", Help.image_site(data.get("imgurl")+""));';
	$dataTrueImgurl='data.put("trueimgurl", Help.image_site(data.get("imgurl")+""));';
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
$inDataUserid='';
//发布时间
$inDataCreatetime="";
$inDataUpdatetime="";
if(in_array("createtime",$fieldKey)){
	$inDataCreatetime='$indata["createtime"]=date("Y-m-d H:i:s");';
}
if(in_array("updatetime",$fieldKey)){
	$inDataUpdatetime=' $indata["updatetime"]=date("Y-m-d H:i:s");';
}
$inDataStatus='';	
switch($_GET["dir"]){
	case "index":
		$isCreateRecommend=false;
		$inDataUserid='$indata["userid"]=$ssuserid;';
		$checkAccess='
			$ssuserid=UserAccess::checkAccess($request); 
			if(!$ssuserid){
				return Help::success(1000,"请先登录");
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
		if(in_array("status",$fieldKey)){
			$inDataStatus='$indata["status"]=0;';
		}	
		$userDataAccess='
			if(empty($row) || $row->userid!=$ssuserid){
				return Help::success(1,"暂无权限");
			}
		'."\r\n";
		break;
	case "admin":
		$isCreateList=false;
		$isCreateShow=false;
		$isCreateMy=false;
		
		$checkAccess=''."\r\n";
		break;
}
//end Action
 
switch($dir){
	case "index":
		break;
	default:
		break;
}

//生成文件
$str='
<?php
namespace app\\'.$mm.'\\'.$comDir.';
use support\Request;
use support\Db;
use ext\DBS;
use ext\UserAccess;
use ext\Help;
class '.$nTable.'
{
';
$str.='	
	/*@@index@@*/    
    public function index(Request $request)
    {
	    $start=$request->get("per_page");
        $limit=12;
        $fm=DBS::MM("'.$mm.'","'.$nTable.'");
        '.$statusWhere.'
		$list=$fm
                ->offset($start)
                ->limit($limit)
                ->whereRaw($where)
				->orderBy("'.$pKey.'","desc")
                ->get();
        $list=$fm->Dselect($list);
        $rscount=$fm->whereRaw($where)->count();
        $per_page=$start+$limit;
        $per_page=$per_page>$rscount?0:$per_page;
        $redata=[
            "error" => 0, 
            "message" => "success",
            "list"=>$list,
            "per_page"=>$per_page,
            "rscount"=>$rscount,
			"limit"=>$limit
        ];
		return json($redata); 
         
		   
    }
';

$isCreateList && $str.='
	/*@@list@@*/    
    public function list(Request $request)
    {
	    $start=$request->get("per_page");
        $limit=12;
        $fm=DBS::MM("'.$mm.'","'.$nTable.'");
        '.$statusWhere.'
		$list=$fm
                ->offset($start)
                ->limit($limit)
                ->whereRaw($where)
				->orderBy("'.$pKey.'","desc")
                ->get();
        $list=$fm->Dselect($list);
        $rscount=$fm->whereRaw($where)->count();
        $per_page=$start+$limit;
        $per_page=$per_page>$rscount?0:$per_page;
        $redata=[
            "error" => 0, 
            "message" => "success",
            "list"=>$list,
            "per_page"=>$per_page,
            "rscount"=>$rscount,
			"limit"=>$limit

        ];
		return json($redata); 
         
		   
    }
';
$isCreateShow && $str.='
	/*@@show@@*/
    public function show(Request $request){
        $'.$pKey.'=$request->get("'.$pKey.'");
        $fm=DBS::MM("'.$mm.'","'.$nTable.'");
        $data=$fm->where("'.$pKey.'",$'.$pKey.')->first();
        if(empty($data) || $data->status >1){
            return Help::success(1,"数据不存在");
        }
        $data->imgurl=Help::images_site($data->imgurl);
        $author=DBS::MM("index","user")->get($data->userid);
        $redata=[
            "error" => 0, 
            "message" => "success",
            "data"=>$data,
            "author"=>$author 
        ];
		return json($redata);       
    } 
';
$isCreateMy && $str.='
	/*@@my@@*/    
    public function my(Request $request)
    {
		'.$checkAccess.'
	    $start=$request->get("per_page");
        $limit=12;
        $fm=DBS::MM("'.$mm.'","'.$nTable.'");
        '.$statusWhere.'
		$where.=" AND userid=".$ssuserid;
		$list=$fm
                ->offset($start)
                ->limit($limit)
                ->whereRaw($where)
				->orderBy("'.$pKey.'","desc")
                ->get();
        $list=$fm->Dselect($list);
        $rscount=$fm->whereRaw($where)->count();
        $per_page=$start+$limit;
        $per_page=$per_page>$rscount?0:$per_page;
        $redata=[
            "error" => 0, 
            "message" => "success",
            "list"=>$list,
            "per_page"=>$per_page,
            "rscount"=>$rscount,
			"limit"=>$limit

        ];
		return json($redata); 
         
		   
    }
';

$isCreateAdd && $str.='
    /*@@add@@*/
	public function add(Request $request){
        '.$checkAccess.'
        $'.$pKey.'=intval($request->get("'.$pKey.'"));
        $data=[];
        if($'.$pKey.'){
            $fm=DBS::MM("'.$mm.'","'.$nTable.'");
            $data=$fm->find($'.$pKey.');
            '.$userDataAccess.'
        }
        $redata=[
            "error" => 0, 
            "message" => "success",
            "data"=>$data 
        ];
		return json($redata);       
    } 
    
';
$isCreateSave &&  $str.='	
    /*@@save@@*/
	public function save(Request $request){
       '.$checkAccess.'
        $'.$pKey.'=intval($request->post("'.$pKey.'"));
        $data=[];
        $fm=DBS::MM("'.$mm.'","'.$nTable.'");
        $indata=[];
        //处理发布内容
        '.$savePost.'
        if($'.$pKey.'){
            $row=$fm->find($'.$pKey.');
            '.$userDataAccess.'
        }
        if($'.$pKey.'){
            '.$inDataUpdatetime.'
            $fm->where("'.$pKey.'",$'.$pKey.')->update($indata);
        }else{       
            '.$inDataUserid.'
            '.$inDataCreatetime.'
            '.$inDataUpdatetime.'
			'.$inDataStatus.'
            $'.$pKey.'=$fm->insertGetId($indata);
        }
      
       
        $redata=[
            "error" => 0, 
            "message" => "保存成功",
            "insert_id"=>$'.$pKey.'
        ];
		return json($redata); 
    }
';
$isCreateStatus &&  $str.='
    /*@@status@@*/
    public function Status(Request $request){
		'.$checkAccess.'
        $'.$pKey.'=$request->get("'.$pKey.'");
        $fm=DBS::MM("'.$mm.'","'.$nTable.'");
        $row=$fm->where("'.$pKey.'",$'.$pKey.')->first();
		'.$userDataAccess.'
        if($row->status==1){
            $status=2;
        }else{
            $status=1;
        }
        $up=$fm->find($'.$pKey.');
        $up->status=$status;
        $up->save();
        $redata=[
            "error" => 0, 
            "message" => "success",
            "status"=>$status
        ];
		return json($redata); 
    }
';
$isCreateRecommend && $str.='
    /*@@recommend@@*/
    public function recommend(Request $request){
		'.$checkAccess.'
        $'.$pKey.'=$request->get("'.$pKey.'");
       $fm=DBS::MM("'.$mm.'","'.$nTable.'");
        
        $row=$fm->where("'.$pKey.'",$'.$pKey.')->first();
		'.$userDataAccess.'
        if($row->isrecommend==1){
            $isrecommend=0;
        }else{
            $isrecommend=1;
        }
         
        $row->isrecommend=$isrecommend;
        $row->save();
        $redata=[
            "error" => 0, 
            "message" => "success",
            "isrecommend"=>$isrecommend
        ];
		return json($redata); 
    }
';
$isCreateDelete && $str.='
    /*@@delete@@*/
    public function delete(Request $request){
		'.$checkAccess.'
        $'.$pKey.'=$request->get("'.$pKey.'");
        $fm=DBS::MM("'.$mm.'","'.$nTable.'");
        $row=$fm->find($'.$pKey.'); 
        '.$userDataAccess.'
        $row->status=11;
        $row->save();
        $redata=[
            "error" => 0, 
            "message" => "success"
        ];
		return json($redata); 
    }
';
$str.='      
}

?>
';

echo "<textarea style='width:1024px;height:400px;'>$str</textarea>";
?>
 