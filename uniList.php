<?php
/**
*Author 雷日锦 362606856@qq.com
*根据表显示数据列表
*/
require("config.php");
require("nav.php");
 
?>
<form>
表名：<input type='text' value="<?=$_GET["table"]?>" name='table'>
目录：
     
      <select name="dir">
     	<?php
			$tpls=array("index","admin","shopadmin");
        	foreach($tpls as $v){
				if($v==$_GET['dir']){
					echo '<option value="'.$v.'" selected>'.$v.'</option>';
				}else{
					echo '<option value="'.$v.'">'.$v.'</option>';
				}
			}
		?>
     </select>
	 <input type='submit' value='生成' >
	 </form><br>
<?php
if(!isset($_GET['table']) or empty($_GET['table']) ){
	
	exit;
}else{
	$table=trim($_GET['table']);
	$data=trim($_GET['data']);
	$dir=$_GET["dir"];
	switch($dir){
		case "index":
			$dir="";
			break;
		case "admin":
			$dir="/admin";
			break;
	}
	$bTable=str_replace("mod_","",$table);
	$aa=explode("_",$bTable);
	$nTable="";
	foreach($aa as $v){
		$nTable.=ucwords($v);
	}	
	$tableModel=$nTable."Model";
}
$res=mysqli_query($link,"show full columns from {$tablepre}{$table}");
 
$form=" <table class=\"tbs\">\r\n";
$f1="  <tr>\r\n";
$f2=' <tr v-for="(item,index) in list" :key="index">'."\r\n";
$fields=[];
$unFields=[
	"content","address","description","imgsdata","tags","keywords"
];
//主键
$pKey="";
$i=0;
$fieldKey=[];
while($rs=mysqli_fetch_array($res,MYSQLI_ASSOC)){
	$fieldKey[]=$rs["Field"]; 
	if($i==0){
		$pKey=$rs['Field'];
	}
	$i++;
	if(in_array($rs["Type"],array("text","mediumtext")) ) continue;
	//判断长度
	preg_match("/\((\d+)\)/",$rs["Type"],$arrType);
 
	if($arrType[1]>225){
		continue;
	}
	if(!in_array($rs["Field"],$unFields)){
		$fields[]=$rs;
	}
}	
$searchCat=<<<eof
<text class="mgr-5">分类：</text>
					<input type="text" class="none" name="catid" v-model="catid" />
					<select v-model="catid"  class="input-flex-select mgr-5 w150">
						<option value="0">请选择</option>

						<block v-for="(one,oi) in catList" :key="one.catid">
							<option :value="one.catid">{{one.cname}}</option>
							<block v-for="(two,bi) in one.child" :key="two.catid">
								<option :value="two.catid">|--{{two.cname}}</option>
								<block v-for="(three,ci) in two.child" :key="three.catid">
									<option :value="three.catid">|----{{three.cname}}</option>
								</block>
							</block>
						</block>

					</select>
eof;
if(!in_array("catid",$fieldKey)){
	$searchCat="";
}
$searchTitle=<<<eof
<text class="mgr-5">主题：</text>
<input type="text" class="w150 mgr-5 input-text" name="title" v-model="title" />
eof;
if(!in_array("title",$fieldKey)){
	$searchTitle="";
}
$searchRecommend=<<<eof
<text class="mgr-5">推荐：</text>
					<select v-model="recommend" class="w100  mgr-5 ">
						<option value="">请选择</option>
						<option value="yes">是</option>
						<option value="no">否</option>
					</select>
eof;
if(!in_array("isrecommend",$fieldKey)){
	$searchRecommend="";
}

$search=<<<eof
<div class="search-form">
			<form @submit="search">
				<div class="flex flex-ai-center">
					<div class="none">
						<input type="text" name="recommend" v-model="recommend" />
						<input type="text" name="type" v-model="type" />
					</div>

					<text class="mgr-5">ID:</text>
					<input class="w100 mgr-5 input-text" type="text" name="{$pKey}" v-model="id" />
					<text>状态：</text>
					<select v-model="type" class="w100 mgr-5">
						<option value="all">全部</option>
						<option value="new">未审核</option>
						<option value="pass">已审核</option>
						<option value="forbid">已禁止</option>
					</select>
					{$searchCat}
					{$searchTitle}
					{$searchRecommend}

					<button form-type="submit" class="btn">搜索</button>
					<div class="flex-1"></div>
				</div>
			</form>
		</div>
eof;
foreach($fields as $k=>$rs){
	if($k>8) break;
	$f=explode(" ",$rs['Comment']);
	$f1.="   <td>".($rs['Comment']?$f[0]:$rs['Field'])."</td>\r\n"; 
	switch($rs["Field"]){
		case "imgurl":
		case "logo":
		case "user_head":
			$f2.='<td><image :src="item.'.$rs['Field'].'" mode="widthFix" class="wh-60" ></image></td>'."\r\n";
			break;
		case "status":
			$f2.='<td><div @click="toggleStatus(item)" :class="item.status==1?\'yes\':\'no\'"></div></td>'."\r\n";
			break;
		case "isrecommend":
			$f2.='<td><div @click="toggleRecommend(item)" :class="item.isrecommend==1?\'yes\':\'no\'"></div></td>'."\r\n";
			break;
		default:
			$f2.="   <td>{{item.".$rs['Field']."}}</td>\r\n";
			break;
	}
	

}
$f1.="<td>操作</td></tr>\r\n";
$f2.='<td>
	<div class="btn-small mgr-5" @click="goAdd(item.'.$pKey.')">编辑</div>

	 
					<div class="btn-small btn-danger" @click="del(item)">删除</div>
</td>'."\r\n";
$f1.="  </tr>\r\n";
$f2.="  </tr>\r\n";
$form.="<thead>".$f1."</thead>";
$form.=$f2;
$form.=" </table>\r\n";

 

$js=<<<eof
<script>
	export default {
		data: function() {
			return {
				pageLoad: false,
				list: [],
				per_page: 0,
				isFirst: true,
				pageList:[],
				aPage:0,
				type:"all",
				id:0,
				recommend:""
			}
		},
		onLoad: function() {
			this.getPage();
		},
		onReachBottom: function() {
			this.getList();
		},
		onPullDownRefresh: function() {
			this.getPage();
			uni.stopPullDownRefresh();
		},
		onShareAppMessage: function() {

		},
		onShareTimeline: function() {

		},
		methods: {
			gourl: function(url) {
				uni.navigateTo({
					url: url
				})
			},
			setPage:function(per_page){
				 
				var that=this;
				that.aPage=per_page;
				that.per_page=per_page;
				that.isFirst=true;
				that.getList();
			}, 
			getPage: function() {
				var that = this;
				that.app.get({
					url: that.app.apiHost + "{$dir}/{$bTable}/index",
					success: function(res) {
						that.pageLoad = true;
						that.list = res.list;
						that.per_page = res.per_page;
						that.pageList=that.app.pageList(res.rscount,res.limit,res.per_page);
					}
				})
			},
			getList: function() {
				var that = this;
				if (that.per_page == 0 && !that.isFirst) {
					return false;
				}
				that.app.get({
					url: that.app.apiHost + "{$dir}/{$bTable}/index",
					data: {
						per_page: that.per_page
					},
					success: function(res) {
						that.per_page = res.per_page;
						if (that.isFirst) {
							that.list = res.list;
							that.isFirst = false;
						} else {
							for (var i in res.list) {
								that.list.push(res.list[i]);
							}
						}

					}
				})
			},
			toggleStatus:function(item){
				var that=this;
				that.app.get({
					url:that.app.apiHost+"{$dir}/{$bTable}/status",
					data:{
						{$pKey}:item.{$pKey}
					},
					success:function(res){
						item.status=res.status;
					}
				})
			},
			toggleRecommend:function(item){
				var that=this;
				that.app.get({
					url:that.app.apiHost+"{$dir}/{$bTable}/recommend",
					data:{
						{$pKey}:item.{$pKey}
					},
					success:function(res){
						item.is_recommend=res.is_recommend;
					}
				})
			},
			del:function(item){
				var that=this;
				uni.showModal({
					content:"确认删除吗",
					success:function(res){
						if(!res.confirm){
							return false;
						}
						that.app.get({
							url:that.app.apiHost+"{$dir}/{$bTable}/delete",
							data:{
								{$pKey}:item.{$pKey}
							},
							success:function(res){
								if(res.error){
									return false;
								}
								var list=[];
								for(var i in that.list){
									if(that.list[i].{$pKey}!=item.{$pKey}){
										list.push(that.list[i]);
									}
								}
								that.list=list;
							}
						})
					}
				})
			},
			goAdd:function({$pKey}){
				uni.navigateTo({
					url:"add?{$pKey}="+{$pKey}
				})
			},
			goShow:function(id){
				uni.navigateTo({
					url:"show?{$pKey}="+id
				})
			},
			search: function(e) {
				var that = this;
				
				that.app.get({
					url: that.app.apiHost + "{$dir}/{$bTable}/index",
					data: e.detail.value,
					success:function(res) {
						that.list = res.list;
					}
				})
			}
		},
	}
</script>

<style>
</style>
eof;
$pagelist=<<<eof
<div class="flex row-box">
				<div :class="item.per_page==aPage?'cl-red':''" class="pd-10 pointer" v-for="(item,index) in pageList" @click="setPage(item.per_page)" :key="index">{{item.value}}</div>
			</div>
eof; 
 echo "<textarea style=\"width:1024px;height:400px;\">
<template>
	<view>
		<div class=\"tabs-border\">
			<div @click=\"gourl('index')\" class=\"item active\">列表</div>
			<div @click=\"gourl('add')\" class=\"item\">添加</div>
		</div>
		".$search."
		".htmlspecialchars($form)."
		".$pagelist."
	</view>
</template>
".$js."
</textarea>";
function getpost($Type,$field){
	$str=strtolower(substr($Type,0,strpos($Type,"(")));
	switch($str){
		case "int":
		case "smallint":
		case "tinyint":
		case "bigint":
			return 'get_post("'.$field.'","i")';
		break;
		
		case "decimal":
			$d=explode(",",$Type);
			$d=intval($d[1]);
			return 'round(get_post("'.$field.'"),'.$d.')';
		default:
			return 'get_post("'.$field.'","h")';
		break;
	}
}
?>