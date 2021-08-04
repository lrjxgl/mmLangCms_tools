<?php
/**
*Author 雷日锦 362606856@qq.com
*根据数据表生成表单
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
	$iswap=intval($_GET["iswap"]);
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
$form1="<form @submit=\"submit\">\r\n";
if(!$iswap){
 
	$form=' <table class="table-add">';
}
$fieldKey=[];
$pKey="";
$saveFields="\r\n";
while($rs=mysqli_fetch_array($res,MYSQLI_ASSOC)){
	$fieldKey[]=$rs["Field"];
	$f=explode(" ",$rs['Comment']);
	$saveFields.=''.$rs["Field"].': "'.getpost($rs["Type"]).'",'."\r\n";
	if($i==0){
		$pKey=$rs['Field'];
		$i++;
		$form1.="<input type=\"text\" name=\"".$rs['Field']."\" class=\"none\" v-model=\"data.{$rs['Field']}\" >\r\n";
		
	}else{
		switch($rs['Field']){
			case "status":
			
			
				if($iswap){
					$form.='<div class="input-flex">
						<div class="input-flex-label">'.($rs['Comment']?$f[0]:$rs['Field']).'：</div>		
						<div class="flex-1">
							<input type="text" {if $data.status eq 1}checked{/if} name="status"  value="1" > 上线
							<input type="text" {if $data.status neq 1}checked{/if} name="status"  value="2" > 下线
						</div>
						</div>'."\r\n";
				}else{
					$form.='  <tr>
						<td>'.($rs['Comment']?$f[0]:$rs['Field']).'：</td>		
						<td>
						<input type="text" class="none" name="status" v-model="data.status" />
						<radio-group>
							<radio type="text" class="mgr-5" checked="data.status==1"  value="1" > 上线</radio>
							<radio type="text" checked="data.status!=1"    value="2" > 下线</radio>
						</radio-group>	
						</td>
						</tr>'."\r\n";
				}
				break;
			case "is_open":
			case "isnew":
			case "isrecommend":
				$form.='  <tr>
						<td>'.($rs['Comment']?$f[0]:$rs['Field']).'：</td>		
						<td>
						<input type="text" class="none" name="'.$rs['Field'].'" v-model="data.'.$rs['Field'].'" />
						<radio-group>
							<radio type="text" class="mgr-5" checked="data.'.$rs['Field'].'==1"  value="1" > 是</radio>
							<radio type="text" class="mgr-5" checked="data.'.$rs['Field'].'!=1"    value="0" > 否</radio>
						</radio-group>	
						</td>
						</tr>'."\r\n";
				break;
			case "imgurl":
			case "logo":
			case "user_head":
				$form.='
					<tr>
							<td>图片：</td>
							<td>
								<input class="none" type="text" name="'.$rs['Field'].'" v-model="data.'.$rs['Field'].'" />
								<sky-upimg @call-parent="setImgurl" :imgurl="data.'.$rs['Field'].'" :trueimgurl="data.true'.$rs['Field'].'">
								</sky-upimg>
							</td>
						</tr>
				'."\r\n";
				break;
			default:
				if($iswap){
					$form.='<div class="input-flex">
						<div class="input-flex-label">'.($rs['Comment']?$f[0]:$rs['Field']).'：</div>		
						<input type="text" name="'.$rs['Field'].'"  class="input-flex-text"  v-model="data.'.$rs["Field"].'"   /> 
						</div>'."\r\n";
				}else{
					$form.='  <tr>
						<td>'.($rs['Comment']?$f[0]:$rs['Field']).'：</td>		
						<td><input class="input-text" type="text" name="'.$rs['Field'].'"   v-model="data.'.$rs["Field"].'" ></td>
						</tr>'."\r\n";
				}
				break;
		}
		
			
		 
	}
}
if(!$iswap){
	$form.="</table>";
}

$form.=" <button form-type=\"submit\" class=\"btn-row-submit\">保存</button> \r\n";
$form=$form1.$form."</form>";
//js
$js=<<<eof
<script>
	import upimgBox from "../../components/upimgbox.vue";
	import skyUpimg from "../../components/skyupimg.vue";
	export default {
		components: {
			upimgBox,
			skyUpimg
		},
		data: function() {
			return {
				'.$pKey.': 0,
				pageLoad: false,
				data: {},
				imgsList: []
			}
		},
		onLoad: function(ops) {
			if (ops.'.$pKey.' != undefined) {
				this.'.$pKey.' = ops.id;
			}
			console.log("onLoad")
			this.getPage();
		},
		methods: {
			gourl: function(url) {
				uni.navigateTo({
					url: url
				})
			},
			setImgs: function(e) {
				this.data.imgsdata = e;
			},
			setImgurl: function(e) {
				this.data.imgurl = e;
			},
			getPage: function() {
				var that = this;
				that.app.get({
					url: that.app.apiHost + "{$dir}/{$bTable}/add",
					data: {
						'.$pKey.': this.'.$pKey.'
					},
					success: function(res) {
						if (Object.keys(res.data).length > 0) {
							that.data = res.data;
							that.imgsList = res.imgsdata;
						} else {
							that.data = {
								{$saveFields}
							}
						}

						that.pageLoad = true;
					}
				})
			},
			submit: function(e) {
				var that = this;
				that.app.post({
					url: that.app.apiHost + "{$dir}/{$bTable}/save",
					data: e.detail.value,
					success: function(res) {
						uni.showToast({
							title: res.message
						})
					}
				})

			}
		}
	}
</script>

<style>
</style>
eof;

 echo "<textarea style=\"width:1024px;height:480px;\">
 <template>
	<view>
	<div class=\"tabs-border\">
			<div @click=\"gourl('index')\" class=\"item\">列表</div>
			<div @click=\"gourl('add')\" class=\"item active\">添加</div>
		</div>
".htmlspecialchars($form)."
	</view>
</template>
".$js."
</textarea>";
function getpost($Type){
	$str=strtolower(substr($Type,0,strpos($Type,"(")));
	switch($str){
		case "int":
		case "smallint":
		case "tinyint":
		case "bigint":
			return 0;
		break;
		
		case "decimal":
			 
			return 0;
		default:
			return "";
		break;
	}
}
?>