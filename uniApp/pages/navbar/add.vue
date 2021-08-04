<template>
	<view>
	<div class="tabs-border">
			<div @click="gourl('index')" class="item">列表</div>
			<div @click="gourl('add')" class="item active">添加</div>
		</div>
<form @submit="submit">
<input type="text" name="id" class="none" v-model="data.id" >
 <table class="table-add">  <tr>
						<td>名称：</td>		
						<td><input class="input-text" type="text" name="title"   v-model="data.title" ></td>
						</tr>
  <tr>
						<td>排序：</td>		
						<td><input class="input-text" type="text" name="orderindex"   v-model="data.orderindex" ></td>
						</tr>
  <tr>
						<td>链接地址：</td>		
						<td><input class="input-text" type="text" name="link_url"   v-model="data.link_url" ></td>
						</tr>
  <tr>
						<td>跳转目标：</td>		
						<td><input class="input-text" type="text" name="target"   v-model="data.target" ></td>
						</tr>
  <tr>
						<td>上级：</td>		
						<td><input class="input-text" type="text" name="pid"   v-model="data.pid" ></td>
						</tr>
  <tr>
						<td>分组：</td>		
						<td><input class="input-text" type="text" name="group_id"   v-model="data.group_id" ></td>
						</tr>
  <tr>
						<td>m：</td>		
						<td><input class="input-text" type="text" name="m"   v-model="data.m" ></td>
						</tr>
  <tr>
						<td>a：</td>		
						<td><input class="input-text" type="text" name="a"   v-model="data.a" ></td>
						</tr>
  <tr>
						<td>状态：</td>		
						<td>
						<input type="text" class="none" name="status" v-model="data.status" />
						<radio-group>
							<radio type="text" class="mgr-5" checked="data.status==1"  value="1" > 上线</radio>
							<radio type="text" checked="data.status!=1"    value="2" > 下线</radio>
						</radio-group>	
						</td>
						</tr>

					<tr>
							<td>图片：</td>
							<td>
								<input class="none" type="text" name="logo" v-model="data.logo" />
								<sky-upimg @call-parent="setImgurl" :imgurl="data.logo" :trueimgurl="data.truelogo">
								</sky-upimg>
							</td>
						</tr>
				
  <tr>
						<td>图标：</td>		
						<td><input class="input-text" type="text" name="icon"   v-model="data.icon" ></td>
						</tr>
</table> <button form-type="submit" class="btn-row-submit">保存</button> 
</form>
	</view>
</template>
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
				'.id.': 0,
				pageLoad: false,
				data: {},
				imgsList: []
			}
		},
		onLoad: function(ops) {
			if (ops.'.id.' != undefined) {
				this.'.id.' = ops.id;
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
					url: that.app.apiHost + "/admin/navbar/add",
					data: {
						'.id.': this.'.id.'
					},
					success: function(res) {
						if (Object.keys(res.data).length > 0) {
							that.data = res.data;
							that.imgsList = res.imgsdata;
						} else {
							that.data = {
								
id: "",
title: "",
orderindex: "0",
link_url: "",
target: "",
pid: "",
group_id: "0",
m: "",
a: "",
status: "0",
logo: "",
icon: "",

							}
						}

						that.pageLoad = true;
					}
				})
			},
			submit: function(e) {
				var that = this;
				that.app.post({
					url: that.app.apiHost + "/admin/navbar/save",
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